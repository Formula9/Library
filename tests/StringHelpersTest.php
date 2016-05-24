<?php namespace Nine\Library;

/**
 * @package Radium Codex
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */
use Nine\Collections\Attributes;

/**
 * Test the framework support functions
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class StringHelpersTest extends \PHPUnit_Framework_TestCase
{
    public function test00_Instantiate()
    {
        //echo "\n+strings";
    }

    public function test01_PartOne()
    {
        ### contains($needles, $haystack)

        $this->assertTrue(Lib::str_has('this exists', 'Testing to see if this exists.'));
        $this->assertFalse(Lib::str_has('this does not exist', 'Testing to see if this exists.'));

        ### slug_to_title($slug)

        $this->assertEquals('Imagine That This Is A Slug', Lib::slug_to_title('imagine-that-this-is-a-slug'));

        ### remove_namespace($class_name, $class_suffix = NULL)

        $this->assertEquals('Attributes', Lib::remove_namespace(Attributes::class));
        $this->assertEquals('Attributes', Lib::remove_namespace(Attributes::class, 'F9'));

        ### name_from_class($class_name, $suffix_to_remove = 'HttpController')

        $this->assertEquals('applicationcontroller', Lib::alias_from_class('F9\ApplicationController', ''));
        $this->assertEquals('application', Lib::alias_from_class('F9\ApplicationController', 'Controller'));

    }

    public function test02_PartTwo()
    {
        ### startsWith($needle, $haystack)

        $this->assertTrue(Lib::starts_with('Odd', 'Odd Greg'));
        $this->assertFalse(Lib::starts_with('Greg', 'Odd Greg'));

        ### endsWith($needle, $haystack)

        $this->assertTrue(Lib::ends_with('Greg', 'Odd Greg'));
        $this->assertFalse(Lib::ends_with('Odd', 'Odd Greg'));

        ### stripTrailing($characters, $string)

        $this->assertEquals('All_The_Things', Lib::strip_tail('_', 'All_The_Things__'));
        $this->assertNotEquals('All_The_Things', Lib::strip_tail('_', 'All_The_Things--'));

        ### truncate($string, $endlength = "30", $end = "...")

        $this->assertEquals('A line tha...',
            Lib::truncate(
                "A line that is in need of shortening and I ain't talking about cooking.",
                $endlength = '10',
                $end = '...'
            )
        );

    }

    public function test03_PartThree()
    {
        ### snakecase_to_heading($word, $space = ' ')

        $this->assertEquals('No Way Bob', Lib::snake_to_heading('no_way_bob'));
        $this->assertEquals('No&nbsp;Way&nbsp;Bob', Lib::snake_to_heading('no_way_bob', '&nbsp;'));

        ### snakecase_to_camelcase($string)

        $this->assertEquals('noWayBob', Lib::snake_to_camel('no_way_bob', true));

        ### camel_to_snakecase($input, $delimiter = '_')

        $this->assertEquals('not_a_chance_bob', Lib::camel_to_snake('notAChanceBob', $delimiter = '_'));

    }

    public function test04_PartFour()
    {
        ### remove_quotes($string)

        $this->assertEquals('A string chock full of quotes', Lib::remove_quotes('A "string" \'chock full\' of \'"quotes"\''));

        ### generate_token($length = 16)

        $this->assertNotSame(Lib::generate_token(), Lib::generate_token());
        # generate_token generates HEX pairs, thus a length of 10 == 20 in the result
        $this->assertSame(strlen(Lib::generate_token(10)), 20);

        ### e($value)

        $this->assertEquals('A &#039;quote&#039; is &lt;b&gt;bold&lt;/b&gt;', Lib::entities("A 'quote' is <b>bold</b>"));

        ### h($string, $double_encode = TRUE)

        /** @noinspection HtmlUnknownTarget */
        $this->assertEquals("&lt;a href='test'&gt;Test&lt;/a&gt;", Lib::hsc("<a href='test'>Test</a>"));

    }

    public function test05_PartFive()
    {
        ### encode_readable_json($to_convert, $indent = 0)

        //file_put_contents(__DIR__ . '/readable_jason.json', encode_readable_json([
        //    'a' => 1,
        //    'b' => 'stuff',
        //    'c' => ['d' => TRUE],
        //    'n' => NULL,
        //]));

        $readable_json = file_get_contents(__DIR__ . '/readable_jason.json');
        $this->assertEquals($readable_json, Lib::encode_readable_json(
            [
                'a' => 1,
                'b' => 'stuff',
                'c' => ['d' => TRUE],
                'n' => NULL,
            ]
        )
        );

        ### parse_class_name($name)

        $expect = [
            'namespace'      =>
                [
                    0 => 'Symfony',
                    1 => 'Component',
                    2 => 'HttpFoundation',
                ],
            'class_name'     => 'AcceptHeader',
            'namespace_path' => 'Symfony\\Component\\HttpFoundation',
            'namespace_base' => 'Symfony',
        ];

        $this->assertEquals($expect, Lib::parse_class_name('Symfony\Component\HttpFoundation\AcceptHeader'));
    }

    public function test06_PartSix()
    {
        ### http_code($key, $default = NULL)

        //$this->assertEquals('404 Not Found', Lib::http_error_from_code(404));
        //$this->assertEquals(502, Lib::http_error_from_code('Bad Gateway'));
        //$this->assertEquals(900, Lib::http_error_from_code('Purple Rain', 900));

        ### file_in_path($name, Array $paths)

        $this->assertStringEndsWith('readable_jason.json', Lib::file_in_path('readable_jason.json', [__DIR__ . '/']));

        ### format_for_url($string)

        $this->assertEquals('the-balls-are-bouncy-eh', Lib::str_to_uri('The balls are bouncy, eh?'));
    }

}
