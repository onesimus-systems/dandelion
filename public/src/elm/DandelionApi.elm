module DandelionApi exposing
    ( ApiMetadata
    , ApiResponse
    , Category
    , CategoryGetAllResp
    , CheestoApiData
    , CheestoApiResp
    , CheestoStatus
    , CheestoUpdate
    , LogEntry
    , LogsApiData
    , LogsApiMetadata
    , LogsApiResp
    , apiDecoder
    , categoriesGetAll
    , categoryDecoder
    , categoryGetAllDataDecoder
    , categoryGetAllDecoder
    , cheestoReadAll
    , cheestoUpdate
    , logDecoder
    , logsApiDataDecoder
    , logsApiRespDecoder
    , logsGet
    , logsGetWithOffset
    , logsListDecoder
    , logsMetadataDecoder
    , logsMetadataEncoder
    , logsSearch
    )

import Http
import Json.Decode as Decode exposing (Decoder, bool, int, list, string)
import Json.Decode.Pipeline exposing (optional, required)
import Json.Encode as Encode
import Url.Builder as UB



-- HTTP


categoriesGetAll : (Result.Result Http.Error CategoryGetAllResp -> msg) -> Cmd msg
categoriesGetAll toMsg =
    Http.get
        { url = "api/i/categories/getall"
        , expect = Http.expectJson toMsg categoryGetAllDecoder
        }


logsGetWithOffset : (Result.Result Http.Error LogsApiResp -> msg) -> Int -> Cmd msg
logsGetWithOffset toMsg offset =
    Http.get
        { url = getLogEntriesUrl offset
        , expect = Http.expectJson toMsg logsApiRespDecoder
        }


logsGet : (Result.Result Http.Error LogsApiResp -> msg) -> Cmd msg
logsGet toMsg =
    logsGetWithOffset toMsg 0


logsSearch : (Result.Result Http.Error LogsApiResp -> msg) -> String -> Int -> Cmd msg
logsSearch toMsg query offset =
    Http.get
        { url = searchApiUrl query offset
        , expect = Http.expectJson toMsg logsApiRespDecoder
        }


cheestoReadAll : (Result.Result Http.Error CheestoApiResp -> msg) -> Cmd msg
cheestoReadAll toMsg =
    Http.get
        { url = "api/i/cheesto/read"
        , expect = Http.expectJson toMsg cheestoApiRespDecoder
        }


type alias CheestoUpdate =
    { status : String
    , returntime : String
    , message : String
    }


cheestoUpdate : (Result.Result Http.Error ApiMetadata -> msg) -> CheestoUpdate -> Cmd msg
cheestoUpdate toMsg update =
    Http.post
        { url = "api/i/cheesto/update"
        , body =
            urlencodedForm
                [ UB.string "status" update.status
                , UB.string "returntime" update.returntime
                , UB.string "message" update.message
                ]
        , expect = Http.expectJson toMsg apiMetadataDecoder
        }



-- URLs


urlencodedForm : List UB.QueryParameter -> Http.Body
urlencodedForm params =
    UB.toQuery params
        |> String.dropLeft 1
        |> Http.stringBody "application/x-www-form-urlencoded"


getLogEntriesUrl : Int -> String
getLogEntriesUrl offset =
    UB.absolute
        [ "api/i/logs/read" ]
        [ UB.int "offset" offset ]


searchApiUrl : String -> Int -> String
searchApiUrl query offset =
    UB.absolute
        [ "api/i/logs/search" ]
        [ UB.string "query" query, UB.int "offset" offset ]



-- JSON


type alias ApiResponse a value =
    a -> Int -> String -> String -> String -> value


type alias ApiMetadata =
    { errorcode : Int
    , moduleName : String
    , requestTime : String
    , status : String
    }


apiDecoder : ApiResponse a value -> Decoder a -> Decoder value
apiDecoder value decoder =
    Decode.succeed value
        |> required "data" decoder
        |> required "errorcode" int
        |> required "module" string
        |> required "requestTime" string
        |> required "status" string


apiMetadataDecoder : Decoder ApiMetadata
apiMetadataDecoder =
    Decode.map4 ApiMetadata
        (Decode.field "errorcode" int)
        (Decode.field "module" string)
        (Decode.field "requestTime" string)
        (Decode.field "status" string)



-- JSON - Get logs api response


logsApiRespDecoder : Decoder LogsApiResp
logsApiRespDecoder =
    apiDecoder LogsApiResp logsApiDataDecoder


type alias LogsApiResp =
    { data : LogsApiData
    , errorcode : Int
    , moduleName : String
    , requestTime : String
    , status : String
    }


type alias LogsApiData =
    { logs : List LogEntry
    , metadata : LogsApiMetadata
    }


type alias LogEntry =
    { body : String
    , canEdit : Bool
    , category : String
    , dateCreated : String
    , fullname : String
    , id : Int
    , isEdited : Bool
    , numOfComments : Int
    , timeCreated : String
    , title : String
    , userID : Int
    }


logsApiDataDecoder : Decoder LogsApiData
logsApiDataDecoder =
    Decode.succeed LogsApiData
        |> required "logs" logsListDecoder
        |> required "metadata" logsMetadataDecoder


type alias LogsApiMetadata =
    { offset : Int
    , limit : Int
    , logSize : Int
    , resultCount : Int
    }


logsMetadataDecoder : Decoder LogsApiMetadata
logsMetadataDecoder =
    Decode.succeed LogsApiMetadata
        |> required "offset" int
        |> required "limit" int
        |> required "logSize" int
        |> required "resultCount" int


logsMetadataEncoder : LogsApiMetadata -> Encode.Value
logsMetadataEncoder metadata =
    Encode.object
        [ ( "offset", Encode.int metadata.offset )
        , ( "limit", Encode.int metadata.limit )
        , ( "logSize", Encode.int metadata.logSize )
        , ( "resultCount", Encode.int metadata.resultCount )
        ]


logsListDecoder : Decoder (List LogEntry)
logsListDecoder =
    list logDecoder


logDecoder : Decoder LogEntry
logDecoder =
    Decode.succeed LogEntry
        |> required "body" string
        |> optional "canEdit" bool False
        |> required "category" string
        |> required "date_created" string
        |> required "fullname" string
        |> required "id" int
        |> required "is_edited" bool
        |> required "num_of_comments" int
        |> required "time_created" string
        |> required "title" string
        |> required "user_id" int



-- JSON - Cheesto


cheestoApiRespDecoder : Decoder CheestoApiResp
cheestoApiRespDecoder =
    apiDecoder CheestoApiResp cheestoApiDataDecoder


type alias CheestoApiResp =
    { data : CheestoApiData
    , errorcode : Int
    , moduleName : String
    , requestTime : String
    , status : String
    }


type alias CheestoApiData =
    { statusOptions : List String
    , statuses : List CheestoStatus
    }


type alias CheestoStatus =
    { disabled : Bool
    , fullname : String
    , id : Int
    , message : String
    , modified : String
    , returntime : String
    , status : String
    , userId : Int
    }


cheestoApiDataDecoder : Decoder CheestoApiData
cheestoApiDataDecoder =
    Decode.map2 CheestoApiData
        (Decode.field "statusOptions" (list string))
        (Decode.field "statuses" (list cheestoStatusDecoder))


cheestoStatusDecoder : Decoder CheestoStatus
cheestoStatusDecoder =
    Decode.succeed CheestoStatus
        |> required "disabled" bool
        |> required "fullname" string
        |> required "id" int
        |> required "message" string
        |> required "modified" string
        |> required "returntime" string
        |> required "status" string
        |> required "user_id" int



-- JSON - Categories


categoryGetAllDecoder : Decoder CategoryGetAllResp
categoryGetAllDecoder =
    apiDecoder CategoryGetAllResp categoryGetAllDataDecoder


type alias CategoryGetAllResp =
    { data : List Category
    , errorcode : Int
    , moduleName : String
    , requestTime : String
    , status : String
    }


type alias Category =
    { desc : String
    , id : Int
    , parent : Int
    }


categoryGetAllDataDecoder : Decode.Decoder (List Category)
categoryGetAllDataDecoder =
    Decode.list categoryDecoder


categoryDecoder : Decode.Decoder Category
categoryDecoder =
    Decode.map3 Category
        (Decode.field "description" Decode.string)
        (Decode.field "id" Decode.int)
        (Decode.field "parent" Decode.int)
