<?php

namespace webignition\Tests\Url\Query;

use webignition\Url\Configuration;
use webignition\Url\Query\Query;

class QueryTest extends AbstractQueryTest
{
    /**
     * @return array
     */
    public function setHasConfigurationDataProvider()
    {
        return [
            'not has configuration' => [
                'configuration' => null,
                'expectedHasConfiguration' => false,
            ],
            'has configuration' => [
                'configuration' => new Configuration(),
                'expectedHasConfiguration' => true,
            ],
        ];
    }

    /**
     * @dataProvider keyValuePairsDataProvider
     *
     * @param string $queryString
     * @param array $expectedKeyValuePairs
     */
    public function testPairs($queryString, array $expectedKeyValuePairs)
    {
        $query = new Query($queryString);

        $this->assertEquals($expectedKeyValuePairs, $query->pairs());
    }

    /**
     * @dataProvider containsDataProvider
     *
     * @param string $queryString
     * @param string $key
     * @param bool $expectedContains
     */
    public function testContains($queryString, $key, $expectedContains)
    {
        $query = new Query($queryString);

        $this->assertEquals($expectedContains, $query->contains($key));
    }

    /**
     * @return array
     */
    public function containsDataProvider()
    {
        return [
            'null query, null key' => [
                'queryString' => null,
                'key' => null,
                'expectedContains' => false,
            ],
            'empty query, empty key' => [
                'queryString' => '',
                'key' => '',
                'expectedContains' => false,
            ],
            'a=1 does not contain b' => [
                'queryString' => 'a=1',
                'key' => 'b',
                'expectedContains' => false,
            ],
            'a=1 does contain a' => [
                'queryString' => 'a=1',
                'key' => 'a',
                'expectedContains' => true,
            ],
            '%3F=1 does contain ?' => [
                'queryString' => '%3F=1',
                'key' => '?',
                'expectedContains' => true,
            ],
        ];
    }

    /**
     * @dataProvider setDataProvider
     *
     * @param string $queryString
     * @param string $key
     * @param mixed $value
     * @param array $expectedPairs
     */
    public function testSet($queryString, $key, $value, $expectedPairs)
    {
        $query = new Query($queryString);
        $query->set($key, $value);

        $this->assertEquals($expectedPairs, $query->pairs());
    }

    /**
     * @return array
     */
    public function setDataProvider()
    {
        return [
            'set on empty query string' => [
                'queryString' => '',
                'key' => 'a',
                'value' => 1,
                'expectedPairs' => [
                    'a' => 1,
                ],
            ],
            'add: key not present' => [
                'queryString' => 'b=2',
                'key' => 'a',
                'value' => 1,
                'expectedPairs' => [
                    'a' => 1,
                    'b' => 2,
                ],
            ],
            'add: key present; un-encoded' => [
                'queryString' => 'a/a=1',
                'key' => 'a/a',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 2,
                ],
            ],
            'add: key present; encoded' => [
                'queryString' => 'a%2Fa=1',
                'key' => 'a/a',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 2,
                ],
            ],
            'add: key present; existing key un-encoded; addition key encoded' => [
                'queryString' => 'a/a=1',
                'key' => 'a%2Fa',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 2,
                ],
            ],
            'remove from empty query string' => [
                'queryString' => '',
                'key' => 'a',
                'value'=> null,
                'expectedPairs' => [],
            ],
            'remove: key not present' => [
                'queryString' => 'a=1',
                'key' => 'b',
                'value'=> null,
                'expectedPairs' => [
                    'a' => 1,
                ],
            ],
            'remove: key present; un-encoded' => [
                'queryString' => 'a/a=1&b=2',
                'key' => 'a/a',
                'value'=> null,
                'expectedPairs' => [
                    'b' => 2,
                ],
            ],
            'remove: key present; encoded' => [
                'queryString' => 'a%2Fa=1&b=2',
                'key' => 'a/a',
                'value'=> null,
                'expectedPairs' => [
                    'b' => 2,
                ],
            ],
            'remove: key present; existing key un-encoded; addition key encoded' => [
                'queryString' => 'a/a=1&b=2',
                'key' => 'a%2Fa',
                'value'=> null,
                'expectedPairs' => [
                    'b' => 2,
                ],
            ],
        ];
    }

    public function testCreateEmptyQuery()
    {
        $query = new Query();

        $this->assertEquals([], $query->pairs());
        $this->assertEquals('', (string)$query);
    }
}