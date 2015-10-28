<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$server = sprintf('mongodb://%s:%s', getenv('MONGO_PORT_27017_TCP_ADDR'), getenv('MONGO_PORT_27017_TCP_PORT'));
$client = new MongoClient($server);

$db = $client->selectDB('company');
$db->drop();

$faker = Faker\Factory::create();

$offices = [];
foreach (range(0, 2) as $num) {
    $offices[$num] = [
        'address' => $faker->streetAddress,
        'city'    => $faker->city,
        'country' => $faker->country,
        'phones'  => [
            [
                'type'       => 'home',
                'number'     => $faker->phoneNumber,
                'validation' => new MongoRegex('/^+371 67/'),
            ],
            [
                'type'       => 'mobile',
                'number'     => $faker->phoneNumber,
                'validation' => new MongoRegex('/^+371 2/'),
            ],
        ],
    ];
}

$db->selectCollection('offices')->batchInsert($offices);

function create_mongo_date(DateTime $datetime) {
    return new MongoDate($datetime->format('U'), $datetime->format('u'));
};

$employees = [];
foreach (range(0, 9) as $num) {
    $employees[$num] = [
        'first_name' => $faker->firstName,
        'last_name'  => $faker->lastName,
        'age'        => $faker->numberBetween(18, 65),
        'avatar'     => new MongoBinData('__data__', MongoBinData::BYTE_ARRAY),
        'signed_at'  => create_mongo_date($faker->dateTimeBetween('-10 years')),
        'office'     => MongoDBRef::create('offices', $offices[rand(0, 2)]['_id']),
    ];
}

$db->selectCollection('employees')->batchInsert($employees);

echo "Done!\n";
