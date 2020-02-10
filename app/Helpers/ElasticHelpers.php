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
    public static function recreateIndexZic()
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
    }


    /**
     * Delete and create citat index
     */
    public static function recreateIndexCitat()
    {

        $indexExists = \Elasticsearch::connection()->indices()->exists([
            "index" => env("SI4_ELASTIC_CITAT_INDEX", "citat")
        ]);
        if ($indexExists) {
            $deleteIndexArgs = [
                "index" => env("SI4_ELASTIC_CITAT_INDEX", "citat"),
            ];
            \Elasticsearch::connection()->indices()->delete($deleteIndexArgs);
        }

        $createIndexArgs = [
            "index" => env("SI4_ELASTIC_CITAT_INDEX", "citat"),
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
        "citat": {
            "date_detection": false,
            "properties": {
                "citatiAuthors.IME": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "citatiAuthors.PRIIMEK": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "naslov0": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
                    "fields": {
                        "keyword": {
                            "type": "keyword"
                        }
                    }
                },
                "naslov1": {
                    "type": "text",
                    "analyzer": "lowercase_analyzer",
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

    }


    /**
     * Sends a document to elastic search to be indexed
     * @param $zicId Integer entity id to index
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
     * Sends a document to elastic search to be indexed
     * @param $zicId Integer entity id to index
     * @param $body Array body to index
     * @return array
     */
    public static function indexCitat($zicId, $cid, $body)
    {
        $requestArgs = [
            "index" => env("SI4_ELASTIC_CITAT_INDEX"),
            "type" => env("SI4_ELASTIC_CITAT_DOCTYPE"),
            "id" => ZicUtil::citElasticId($zicId, $cid),
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

    public static function searchCit($query, $offset = 0, $limit = 10, $sortField = null, $sortOrder = "asc", $highlight = null)
    {
        $requestArgs = [
            "index" => env("SI4_ELASTIC_CITAT_INDEX"),
            "type" => env("SI4_ELASTIC_CITAT_DOCTYPE"),
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
            //$filters = self::mapFilterFilterFields($filters);
            //print_r($filtersParsed);
            foreach ($filters as $fKey => $fVal) {

                switch ($fKey) {
                    case "authorsShort":
                    case "authorsLong":
                        $must[] = [
                            "query_string" => [
                                "fields" => ["authors.IME", "authors.PRIIMEK"],
                                "query" => join(" OR ", explode(" ", $fVal)),
                            ],
                        ];
                        break;
                    default:

                        // Is Range query?
                        if (strpos($fVal, "..") !== false) {
                            $ltgt = explode("..", $fVal);
                            $gt = isset($ltgt[0]) && $ltgt[0] ? trim($ltgt[0]) : null;
                            $lt = isset($ltgt[1]) && $ltgt[1] ? trim($ltgt[1]) : null;

                            $range = [];
                            if ($gt) $range["gte"] = $gt;
                            if ($lt) $range["lte"] = $lt;

                            $must[] = [
                                "range" => [
                                    $fKey => $range
                                ],
                            ];
                        } else {
                            // Default

                            // Replace whitespace with AND
                            $fVal = join(" AND ", explode(" ", $fVal));
                            // Replace , with OR
                            $fVal = str_replace(",", " OR ", $fVal);

                            $must[] = [
                                "query_string" => [
                                    "fields" => [$fKey],
                                    "query" => $fVal,
                                ],
                            ];
                        }
                        break;
                }
            }
        }

        $query = [ "bool" => [] ];
        if (count($should)) $query["bool"]["should"] = $should;
        if (count($must)) $query["bool"]["must"] = $must;

        //print_r($query);

        return self::search($query, $offset, $limit, $sortField, $sortOrder, null);
    }


    public static function searchCitsString($queryString, $filters, $offset = 0, $limit = 10, $sortField = null, $sortOrder = "asc")
    {

        $searchFields = [
            "citatiAuthors.IME",
            "citatiAuthors.PRIIMEK",
            "naslov0",
            "naslov1",
            "COBISSid",
            "sistoryId",
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

                switch ($fKey) {
                    case "citatiAuthorsShort":
                    case "citatiAuthorsLong":
                        $must[] = [
                            "query_string" => [
                                "fields" => ["citatiAuthors.IME", "citatiAuthors.PRIIMEK"],
                                "query" => join(" OR ", explode(" ", $fVal)),
                            ],
                        ];
                        break;
                    case "zicCompressed":
                        $must[] = [
                            "query_string" => [
                                "fields" => ["zic.OpNaslov", "zic.authors.IME", "zic.authors.PRIIMEK"],
                                "query" => join(" OR ", explode(" ", $fVal)),
                            ],
                        ];
                        break;
                    default:

                        // Is Range query?
                        if (strpos($fVal, "..") !== false) {
                            $ltgt = explode("..", $fVal);
                            $gt = isset($ltgt[0]) && $ltgt[0] ? trim($ltgt[0]) : null;
                            $lt = isset($ltgt[1]) && $ltgt[1] ? trim($ltgt[1]) : null;

                            $range = [];
                            if ($gt) $range["gte"] = $gt;
                            if ($lt) $range["lte"] = $lt;

                            $must[] = [
                                "range" => [
                                    $fKey => $range
                                ],
                            ];
                        } else {
                            // Default

                            // Replace whitespace with AND
                            $fVal = join(" AND ", explode(" ", $fVal));
                            // Replace , with OR
                            $fVal = str_replace(",", " OR ", $fVal);

                            $must[] = [
                                "query_string" => [
                                    "fields" => [$fKey],
                                    "query" => $fVal,
                                ],
                            ];
                        }
                        break;
                }
            }
        }

        $query = [ "bool" => [] ];
        if (count($should)) $query["bool"]["should"] = $should;
        if (count($must)) $query["bool"]["must"] = $must;

        //print_r($query);

        return self::searchCit($query, $offset, $limit, $sortField, $sortOrder, null);
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

    public static function searchCitById($idArray)
    {
        $requestArgs = [
            "index" => env("SI4_ELASTIC_CITAT_INDEX"),
            "type" => env("SI4_ELASTIC_CITAT_DOCTYPE"),
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