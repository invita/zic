<?php
namespace App\Helpers;

/**
 * Class ElasticHelper
 *
 * @author   Matic Vrscaj
 */
class ElasticHelpers
{

    /**
     * Delete and create index
     */
    public static function recreateIndex()
    {

        $indexExists = \Elasticsearch::connection()->indices()->exists([
            "index" => env("SI4_ELASTIC_ZIC_INDEX", "zic")
        ]);
        if ($indexExists) {
            $deleteIndexArgs = [
                "index" => env("SI4_ELASTIC_ZIC_INDEX", "zic"),
            ];
            \Elasticsearch::connection()->indices()->delete($deleteIndexArgs);
        }

        $createIndexArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX", "zic"),
        ];
        $createIndexArgs["body"] = <<<HERE
{
    "settings": {
        "number_of_shards": 1,
        "number_of_replicas": 0,
        "analysis": {
            "analyzer": {
                "lowercase_analyzer": {
                    "type": "custom",
                    "tokenizer": "standard",
                    "filter": [ "lowercase" ]
                }
            }
        }
    },
    "mappings": {
        "zic": {
            "date_detection": false,
            "properties": {
                "authors.IME": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "authors.PRIIMEK": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "OpNaslov": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "OpNaslov": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "OpVzpNaslov": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "OpPodnaslov": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "PvNaslov": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "PvNaslovKratki": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "PvPodnaslov": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "PvVzporedniNaslov": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "OpAvtor0": {
                    "type": "text",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "OpSistoryUrnId": {
                    "type": "text",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                }
            }
        }
    }
}
HERE;

        return \Elasticsearch::connection()->indices()->create($createIndexArgs);

        /*
        $deleteIndexArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => "",
            "id" => "",
        ];
        \Elasticsearch::connection()->delete($deleteIndexArgs);

        $createIndexArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE", "zrtev"),
            "id" => "",
            "body" => []
        ];
        return @\Elasticsearch::connection()->create($createIndexArgs);
        */
    }


    /**
     * Sends a document to elastic search to be indexed
     * @param $zrtevId Integer entity id to index
     * @param $body Array body to index
     * @return array
     */
    public static function indexZic($zicId, $body)
    {
        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "id" => $zicId,
            "body" => $body
        ];
        return \Elasticsearch::connection()->index($requestArgs);
    }

    /**
     * Delete a document from elastic search index
     * @param $zrtevId Integer entity id to delete
     * @return array
     */
    public static function deleteZic($zicId)
    {
        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "id" => $zicId,
        ];
        return \Elasticsearch::connection()->delete($requestArgs);
    }


    /**
     * Retrieves matching documents from elastic search
     * @param $query String to match
     * @param $offset Integer offset
     * @param $limit Integer limit
     * @param $sortField String elastic field name to sort on
     * @param $sortOrder String asc or desc
     * @param $highlight Array highlight parameter
     * @return array
     */
    public static function search($query, $offset = 0, $limit = 10, $sortField = null, $sortOrder = "asc", $highlight = null)
    {
        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "body" => [
                "query" => $query,
                "from" => $offset,
                "size" => $limit,
            ]
        ];

        if ($sortField) {
            $requestArgs["body"]["sort"] = [$sortField => [ "order" => $sortOrder ]];
        }

        if ($highlight) {
            $requestArgs["body"]["highlight"] = $highlight;
        }

        //print_r($requestArgs);

        return \Elasticsearch::connection()->search($requestArgs);
    }


    /*
     * Retrieves all matching documents from elastic search
     * @param $query String to match
     * @param $offset Integer offset
     * @param $limit Integer limit
     * @return array
     */

    // Obsolete
    /*
    public static function search_old($query, $filter, $offset = 0, $limit = 10, $sortField = null, $sortOrder = "asc")
    {
        foreach ($filter as $key => $val) {
            $query .= " AND ".$key.":".$val;
        }

        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "body" => [
                "query" => [
                    "query_string" => [
                        "query" => $query,
                    ]
                ],
                //"filter" => $esFilter,
                //"sort" => "id",
                "from" => $offset,
                "size" => $limit,
            ]
        ];

        if ($sortField) {
            if ($sortField !== "ID") $sortField .= ".keyword";
            $requestArgs["body"]["sort"] = [
                [
                    $sortField => ["order" => $sortOrder]
                ]
            ];
        }

        print_r($requestArgs);

        return \Elasticsearch::connection()->search($requestArgs);
    }
    */


    public static function searchString($queryString, $filters, $offset = 0, $limit = 10, $sortField = null, $sortOrder = "asc")
    {

        $searchFields = [
            "authors.IME",
            "authors.PRIIMEK",
            "OpNaslov",
            "OpCobId",
            "OpSistoryUrnId",
            "PvISSN",
        ];

        $must = [];
        $should = [];

        $must[] = [
            "simple_query_string" => [
                "fields" => $searchFields,
                "query" => $queryString,
                "default_operator" => "and",
            ],
        ];

        if ($filters) {
            foreach ($filters as $fKey => $fVal) {
                $must[] = [
                    "query_string" => [
                        "default_field" => $fKey,
                        "query" => $fVal,
                    ],
                ];
            }
        }

        $query = [ "bool" => [] ];
        if (count($should)) $query["bool"]["should"] = $should;
        if (count($must)) $query["bool"]["must"] = $must;

        //print_r($query);

        return self::search($query, $offset, $limit, $sortField, $sortOrder, null);
    }


    /**
     * Retrieves all matching documents from elastic search
     * @param $query String to match
     * @param $offset Integer offset
     * @param $limit Integer limit
     * @return array
     */
    /*
    public static function searchFilter($filter, $offset = 0, $limit = 10)
    {
        $must = [];
        foreach ($filter as $key => $val) {
            $must[] = ["term" => [$key => $val]];
        }

        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "body" => [
                "query" => [
                    "bool" => [
                        "must" => $must
                    ]
                ],
                //"sort" => "id",
                "from" => $offset,
                "size" => $limit,
            ]
        ];
        return \Elasticsearch::connection()->search($requestArgs);
    }
    */

    public static function searchByIdArray($idArray)
    {
        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "body" => [
                "query" => [
                    "ids" => [
                        "values" => $idArray
                    ]
                ]
            ]
        ];
        $dataElastic = \Elasticsearch::connection()->search($requestArgs);
        return self::mergeElasticResultAndIdArray($dataElastic, $idArray);
    }

    public static function searchById($idArray)
    {
        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "body" => [
                "query" => [
                    "ids" => [
                        "values" => [ $idArray ]
                    ]
                ]
            ]
        ];
        $dataElastic = \Elasticsearch::connection()->search($requestArgs);
        return $dataElastic;
    }

    public static function searchZicByTitle($title) {
        $query = [
            "bool" => [
                "must" => []
            ]
        ];

        // Must match title
        $query["bool"]["must"][] = [
            "term" => [ "OpNaslov.keyword" => $title ]
        ];

        return self::search($query, 0, 1, null, "asc", null);

    }

    public static function searchCitingZics($originalZic)
    {

        //$originalZic["authors"] == $originalZic["citati"][$i]["citatiAuthors"]
        //$originalZic["OpNaslov"] == $originalZic["citati"][$i]["naslov0"]



        //

        /*
        $searchFields = [
            "authors.IME",
            "authors.PRIIMEK",
            "OpNaslov",
            "OpCobId",
            "OpSistoryUrnId",
            "PvISSN",
        ];
        */


        $query = [
            "bool" => [
                "must" => []
            ]
        ];

        // Must match title
        $query["bool"]["must"][] = [
            "term" => [ "citati.naslov0.keyword" => $originalZic["OpNaslov"] ]
        ];

        // Must match at least one author
        /*
        $authorsShould = [];
        $authorsShould[] = [

            "term" => [ "citati.citatiAuthors.IME.keyword" => $originalZic["OpNaslov"] ]
        ]
        $query["bool"]["must"][] = [
            "bool" => [
                "should" => $authorsShould,
                "minimum_should_match" => 1
            ]
        ];
        */

        //print_r($query);

        return self::search($query, 0, 9999, null, "asc", null);

    }


    public static function suggestCreators($creatorTerm, $limit = 30)
    {

        $creatorWords = explode(" ", $creatorTerm);
        $creatorSimple = "";
        if (count($creatorWords) > 0) $creatorSimple .= $creatorWords[0];
        if (count($creatorWords) > 1) $creatorSimple .= " ".$creatorWords[1];

        $queryStringWild = $creatorSimple."*";

        $should = [];
        $should[] = [
            "query_string" => [
                "fields" => [
                    "authors.IME",
                ],
                "query" => $queryStringWild
            ]
        ];

        $should[] = [
            "query_string" => [
                "fields" => [
                    "authors.PRIIMEK",
                ],
                "query" => $queryStringWild
            ]
        ];

        $query = [
            "bool" => [ "should" => $should ]
        ];


        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "body" => [
                "query" => $query,
                "from" => 0,
                "size" => $limit,
            ]
        ];

        //print_r($requestArgs);

        return \Elasticsearch::connection()->search($requestArgs);

    }

    public static function suggestTitlesForCreator($creator, $title, $limit = 30)
    {
        //print_r(["creator" => $creator, "title" => "'".$title."'"]);

        $must = [];
        $should = [];

        /*
        if ($creator) {
            $creatorWords = explode(" ", $creator);
            foreach ($creatorWords as $creatorWord) {
                $should[] = [
                    "query_string" => [
                        "fields" => [
                            "authors.IME",
                        ],
                        "query" => $creatorWord
                    ]
                ];
                $should[] = [
                    "query_string" => [
                        "fields" => [
                            "authors.PRIIMEK",
                        ],
                        "query" => $creatorWord
                    ]
                ];
            }
        }
        */

        if ($creator) {

            $creatorWords = explode(" ", $creator);

            $must[] = [
                "query_string" => [
                    "fields" => [
                        "authors.IME",
                    ],
                    "query" => join(" OR ", $creatorWords)
                ],
            ];
            $must[] = [
                "query_string" => [
                    "fields" => [
                        "authors.PRIIMEK",
                    ],
                    "query" => join(" OR ", $creatorWords)
                ],
            ];
        }


        $titleWords = explode(" ", $title);
        foreach ($titleWords as $titleWord) {
            $must[] = [
                "query_string" => [
                    "fields" => [
                        "OpNaslov",
                    ],
                    "query" => $titleWord."*"
                ],
            ];
        }

        $query = [ "bool" => [] ];

        if ($should) $query["bool"]["should"] = $should;
        if ($must) $query["bool"]["must"] = $must;

        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "body" => [
                "query" => $query,
                "from" => 0,
                "size" => $limit,
            ]
        ];

        //print_r($requestArgs);

        return \Elasticsearch::connection()->search($requestArgs);
    }

    public static $skipChars = [",", "\\."];
    public static function removeSkipCharacters($str) {
        foreach (self::$skipChars as $skipChar) {
            $str = mb_ereg_replace($skipChar, "", $str);
        }
        return $str;
    }




    public static function distinctDezela() {
        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "body" => [
                "aggs" => [
                    "uniq" => [
                        "terms" => [ "field" => "DEZELA.keyword" ]
                    ]
                ],
                "size" => 0,
            ]
        ];

        $elasticResult = \Elasticsearch::connection()->search($requestArgs);
        $result = [];
        if (isset($elasticResult["aggregations"]) && isset($elasticResult["aggregations"]["uniq"])) {
            $buckets = $elasticResult["aggregations"]["uniq"]["buckets"];
            foreach ($buckets as $idx => $bucket) {
                if (!$bucket["key"]) continue;
                $result[] = $bucket["key"];
            }
        }
        return $result;
    }

    public static function distinctObcina() {
        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "body" => [
                "aggs" => [
                    "uniq" => [
                        "terms" => [ "field" => "OBCINA.keyword" ]
                    ]
                ],
                "size" => 0,
            ]
        ];

        $elasticResult = \Elasticsearch::connection()->search($requestArgs);
        $result = [];
        if (isset($elasticResult["aggregations"]) && isset($elasticResult["aggregations"]["uniq"])) {
            $buckets = $elasticResult["aggregations"]["uniq"]["buckets"];
            foreach ($buckets as $idx => $bucket) {
                if (!$bucket["key"]) continue;
                $result[] = $bucket["key"];
            }
        }
        return $result;
    }


    public static function searchChartData($from, $to, $land, $munic) {

        $fromYear = 1910;
        $fromMonth = 0;
        if ($from) {
            $e = explode("-", $from);
            $fromYear = intval($e[0]);
            $fromMonth = intval($e[1]);
        }

        $toYear = 1920;
        $toMonth = 12;
        if ($to) {
            $e = explode("-", $to);
            $toYear = intval($e[0]);
            $toMonth = intval($e[1]);
        }

        $requestArgs = [
            "index" => env("SI4_ELASTIC_ZIC_INDEX"),
            "type" => env("SI4_ELASTIC_ZIC_DOCTYPE"),
            "body" => [
                "aggs" => [
                    "year" => [
                        "terms" => [ "field" => "SMRT_LETO" ],
                        "aggs" => [
                            "month" => [
                                "terms" => [ "field" => "SMRT_MESEC" ],
                            ]
                        ]
                    ]
                ],
                "size" => 0,
            ]
        ];


        $mainBool = [
            "bool" => [
                "must" => [
                    // From
                    [
                        "bool" => [
                            "should" => [ // Either SMRT_LETO > $fromYear
                                ["range" => [
                                    "SMRT_LETO" => [
                                        "gt" => $fromYear,
                                    ]
                                ]],
                                ["bool" => [ // Or SMRT_LETO >= $fromYear AND SMRT_MESEC >= $fromMonth
                                    "must" => [
                                        ["range" => [
                                            "SMRT_LETO" => [
                                                "gte" => $fromYear,
                                            ]
                                        ]],
                                        ["range" => [
                                            "SMRT_MESEC" => [
                                                "gte" => $fromMonth,
                                            ]
                                        ]],
                                    ]
                                ]],
                            ],
                            "minimum_should_match" => 1
                        ]
                    ],
                    // To
                    [
                        "bool" => [
                            "should" => [ // Either SMRT_LETO < $toYear
                                ["range" => [
                                    "SMRT_LETO" => [
                                        "lt" => $toYear,
                                    ]
                                ]],
                                ["bool" => [ // Or SMRT_LETO >= $fromYear AND SMRT_MESEC >= $fromMonth
                                    "must" => [
                                        ["range" => [
                                            "SMRT_LETO" => [
                                                "lte" => $toYear,
                                            ]
                                        ]],
                                        ["range" => [
                                            "SMRT_MESEC" => [
                                                "lte" => $toMonth,
                                            ]
                                        ]],
                                    ]
                                ]],
                            ],
                            "minimum_should_match" => 1
                        ]
                    ],
                ],
            ]
        ];

        if ($land) {
            $mainBool["bool"]["must"][] = ["term" => [ "DEZELA.keyword" => $land ]];
        }

        if ($munic) {
            $mainBool["bool"]["must"][] = ["term" => [ "OBCINA.keyword" => $munic ]];
        }

        $requestArgs["body"]["query"] = $mainBool;

        $elasticResult = \Elasticsearch::connection()->search($requestArgs);
        //print_r($elasticResult);
        $result = [];
        if (isset($elasticResult["aggregations"]) && isset($elasticResult["aggregations"]["year"])) {
            $yearBuckets = $elasticResult["aggregations"]["year"]["buckets"];
            foreach ($yearBuckets as $yIdx => $yearBucket) {
                if (!$yearBucket["key"]) continue;
                $year = $yearBucket["key"];
                $monthBuckets = $yearBucket["month"]["buckets"];
                foreach ($monthBuckets as $mIdx => $monthBucket) {
                    $month = str_pad($monthBucket["key"], 2, "0", STR_PAD_LEFT);
                    $result[$year."-".$month] = $monthBucket["doc_count"];
                }
            }
        }

        return $result;
    }


    public static function elasticResultToAssocArray($dataElastic) {
        $result = [];
        if (isset($dataElastic["hits"]) && isset($dataElastic["hits"]["hits"])) {
            foreach ($dataElastic["hits"]["hits"] as $hit){
                $result[$hit["_id"]] = [
                    "id" => $hit["_id"],
                    "_source" => $hit["_source"],
                ];
            }
        }
        return $result;
    }

    public static function mergeElasticResultAndIdArray($dataElastic, $idArray) {
        $hits = self::elasticResultToAssocArray($dataElastic);

        $result = [];
        foreach ($idArray as $id) $result[$id] = ["id" => $id];
        foreach ($result as $i => $val) {
            if (isset($hits[$i])) $result[$i]["_source"] = $hits[$i]["_source"];
        }
        return $result;
    }

    public static function elasticResultToSimpleAssocArray($dataElastic) {
        $result = [];
        if (isset($dataElastic["hits"]) && isset($dataElastic["hits"]["hits"])) {
            foreach ($dataElastic["hits"]["hits"] as $hit){
                $result[$hit["_id"]] = $hit["_source"];
            }
        }
        return $result;
    }
    public static function elasticResultToSimpleArray($dataElastic) {
        $result = [];
        if (isset($dataElastic["hits"]) && isset($dataElastic["hits"]["hits"])) {
            foreach ($dataElastic["hits"]["hits"] as $hit){
                $result[] = $hit["_source"];
            }
        }
        return $result;
    }

}