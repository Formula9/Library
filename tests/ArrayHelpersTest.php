<?php namespace Nine\Library;

/**
 * Test the framework support functions
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class ArrayHelpersTest extends \PHPUnit_Framework_TestCase
{
    public $source_array = [
        'Apples' => 'One',
        'Beets'  => 2,
        'Candy'  => ['start' => 'now', 'end' => 'then'],
    ];

    public $source_array_table = [
        'Name',
    ];

    public function test00_Instantiate()
    {
        //echo "\n+arrays";
    }

    public function test01_PartOne()
    {
        ### array_to_object

        $obj = Support::cast_array_to_object($this->source_array);
        static::assertEquals($obj->Apples, $this->source_array['Apples']);

        ### object_to_array

        $array = Support::cast_object_as_array($obj);
        static::assertEquals($this->source_array, $array);

        ### copy_object_to_array

        $obj2 = Support::cast_array_to_object(
            [
                'a' => 'not much',
            ]
        );
        $obj1 = Support::cast_array_to_object(
            [
                'apples'      => 10,
                'beets'       => 'nope',
                'candy.start' => 'now',
                'candy.end'   => 'never',
                'value_obj'   => $obj2,
            ]
        );
        $obj_array = Arrays::array_from_object($obj1);
        static::assertEquals(
            [
                'apples'      => 10,
                'beets'       => 'nope',
                'candy.start' => 'now',
                'candy.end'   => 'never',
                'value_obj'   => ['a' => 'not much'],
            ],
            $obj_array
        );

        ### array_insert_before_key

        static::assertEquals(
            [
                'Apples' => 'One',
                'Beets'  => 2,
                'Beef'   => ['hamburger', 'roast beef'],
                'Candy'  => ['start' => 'now', 'end' => 'then'],
            ],
            Arrays::insert_before('Candy', $this->source_array, 'Beef', ['hamburger', 'roast beef'])
        );

        ### array_except($array, $keys)

        static::assertEquals(
            [
                'Apples' => 'One',
                'Candy'  => ['start' => 'now', 'end' => 'then'],
            ],
            Arrays::array_except($this->source_array, ['Beets'])
        );

        ### array_pull(&$array, $key, $default = NULL)

        # copy the test array
        $worker = $this->source_array;
        # pull 'Beets' -> 2
        static::assertEquals(2, Arrays::array_pull($worker, 'Beets', $default = FALSE));
        # verify removed from original
        static::assertArrayNotHasKey('Beets', $worker);

    }

    public function test02_PartTwo()
    {
        # array_dict - array to flattened dictionary

        static::assertEquals(
            [
                'Apples'      => 'One',
                'Beets'       => 2,
                'Candy.start' => 'now',
                'Candy.end'   => 'then',
            ],
            Arrays::array_flatten($this->source_array)
        );

        ### s_to_a - string to array

        static::assertEquals(
            [
                'apples',
                'beets',
                'candy.start',
                'candy.end',
            ],
            Arrays::array_from_string('apples beets candy.start candy.end')
        );

        ### s_to_aa - string to associative array

        static::assertEquals(
            [
                'apples'      => 10,
                'beets'       => 'nope',
                'candy.start' => 'now',
                'candy.end'   => 'never',
            ],
            Arrays::assoc_from_string('apples:10, beets:nope, candy.start:now, candy.end:never')
        );

    }

    public function test03_PartThree()
    {
        ### array_forget(&$array, $keys)

        # forget by single key
        $worker = $this->source_array;
        Arrays::array_forget($worker, 'Candy');
        static::assertEquals(
            [
                'Apples' => 'One',
                'Beets'  => 2,
            ],
            $worker
        );
        # forget by dot path
        $worker = $this->source_array;
        Arrays::array_forget($worker, 'Candy.start');
        static::assertEquals(
            [
                'Apples' => 'One',
                'Beets'  => 2,
                'Candy'  => ['end' => 'then'],
            ],
            $worker
        );

        ### array_extract_list($find_key, $array)

        $records = [
            'George' => ['age' => 26, 'gender' => 'Male'],
            'Lois'   => ['age' => 32, 'gender' => 'Female'],
        ];
        static::assertEquals([26, 32], Arrays::extract_column($records, 'age'));

        ### (simple) array_make_compare_list(array $array)

        $worker = Arrays::assoc_from_string('name:Laura, access:Administrator');
        static::assertEquals(
            [
                'name=`Laura`',
                'access=`Administrator`',
            ],
            Support::make_compare($worker)
        );
        # empty returns null
        static::assertNull(Support::make_compare([]));
        # list returns null on invalid array (must be associative)
        static::assertNull(Support::make_compare(['bad']));
    }

    public function test04_PartFour()
    {
        ### array_fill_object($obj, $array)

        $obj = Support::cast_array_to_object(Arrays::assoc_from_string('name:Greg, location:Vancouver, cat:Julius'));
        static::assertEquals(
            [
                'name'     => 'Greg',
                'location' => 'Vancouver',
                'cat'      => 'Julius',
            ],
            Support::cast_object_as_array($obj)
        );
        $obj = Support::fill_object($obj, Arrays::assoc_from_string('need:Coffee'));
        static::assertEquals(
            [
                'name'     => 'Greg',
                'location' => 'Vancouver',
                'cat'      => 'Julius',
                'need'     => 'Coffee',
            ],
            Support::cast_object_as_array($obj));

    }

    public function test05_PartFive()
    {
        ### generate_object_value_hash($object, $value)

        $obj = new \stdClass();

        static::assertEquals(
            [
                'stdClass' => Arrays::assoc_from_string('one:1, two:2, three:3, four:4'),
            ],
            Support::value_class($obj, Arrays::assoc_from_string('one:1, two:2, three:3, four:4'))
        );

        # non-object returns null
        /** @noinspection PhpParamsInspection */
        static::assertNull(Support::value_class('not an object', Arrays::assoc_from_string('one:1, two:2, three:3, four:4')));

        ### pivot_array_on_index(array $input)

        $worker = [
            [
                'name' => 'Google',
                'url'  => 'https://google.com',
            ],
            [
                'name' => 'Yahoo!',
                'url'  => 'http://yahoo.com',
            ],
        ];
        static::assertEquals(
            [
                'name' =>
                    [
                        'Google',
                        'Yahoo!',
                    ],
                'url'  =>
                    [
                        'https://google.com',
                        'http://yahoo.com',
                    ],
            ],
            Arrays::pivot_array($worker)
        );

        ### array_get($array, $key, $default = NULL)

        static::assertEquals('now', Arrays::array_get($this->source_array, 'Candy.start'));
        static::assertEquals('not found', Arrays::array_get($this->source_array, 'Candy.nope', 'not found'));

        ###  multi_explode(array $delimiters, $string, $trim)

        static::assertEquals(
            [
                0 => 'This is a string',
                1 => ' Break it up',
                2 => ' Ok?',
            ],
            Arrays::multi_explode('This is a string. Break it up! Ok?', ['.', '!'])
        );

        ### convert_list_to_indexed_array($array)

        static::assertEquals(
            [
                0 => 'one',
                1 => 'two',
            ],
            Arrays::array_to_numeric_index(Arrays::array_from_string('one two'))
        );

    }

    public function test06_PartSix()
    {
        $searchRA = [
            'name'   => 'greg',
            'record' => [
                'age'    => 100,
                'amount' => 26.58,
                'source' => 'pension',
            ],
        ];

        static::assertEquals('not found', Arrays::array_get($searchRA, 'record.lazy', 'not found'));
        static::assertEquals($searchRA['record'], Arrays::array_search_and_replace($searchRA, 'record.lazy', 'not found'));
        static::assertEquals(26.58, Arrays::array_get($searchRA, 'record.amount', 'not found'));
    }

}
