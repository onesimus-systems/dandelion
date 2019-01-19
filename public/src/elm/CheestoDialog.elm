module CheestoDialog exposing
    ( NotOkCancel(..)
    , State
    , cheestoDialog
    , closedState
    , getUpdate
    , init
    )

import Browser.Dom as Dom
import Dialogs exposing (..)
import Html
import Html.Styled as HS exposing (..)
import Html.Styled.Attributes exposing (..)
import Html.Styled.Events exposing (onCheck, onInput)
import Styles as S



-- STATE


type State
    = State StateValue


getStateValue : State -> StateValue
getStateValue state =
    case state of
        State stateValue ->
            stateValue


type alias StateValue =
    { message : String
    , returntime : String
    , closed : NotOkCancel
    }


type NotOkCancel
    = Not
    | Ok
    | Cancel



-- STATE GETTERS


closedState : State -> NotOkCancel
closedState state =
    (getStateValue state).closed


getUpdate : State -> { message : String, returntime : String }
getUpdate state =
    let
        stateValue =
            getStateValue state
    in
    { message = stateValue.message
    , returntime = stateValue.returntime
    }



-- INIT


init : State
init =
    State
        { message = ""
        , returntime = "Today"
        , closed = Not
        }



-- UPDATE


type alias ToMsg msg =
    State -> msg


type IntMsg
    = ChangeMessage
    | ChangeReturnTime


updateTextInput : StateValue -> ToMsg msg -> IntMsg -> String -> msg
updateTextInput state toMsg msg val =
    case msg of
        ChangeMessage ->
            toMsg (State { state | message = val })

        ChangeReturnTime ->
            toMsg (State { state | returntime = val })


updateClosedState : StateValue -> ToMsg msg -> Bool -> msg
updateClosedState state toMsg ok =
    let
        newClosedState =
            if ok then
                Ok

            else
                Cancel
    in
    toMsg (State { state | closed = newClosedState })



-- VIEW


cheestoDialog : ToMsg msg -> State -> Html.Html msg
cheestoDialog toMsg state =
    let
        stateValue =
            getStateValue state
    in
    toUnstyled (view stateValue toMsg)


view : StateValue -> ToMsg msg -> Html msg
view state toMsg =
    let
        defaultConfig =
            Dialogs.defaultDialogConfig
    in
    dialogWithConfig
        { defaultConfig | title = "Cheesto Status" }
        (viewDialog state toMsg)
        (updateClosedState state toMsg)


viewDialog : StateValue -> ToMsg msg -> List (Html msg)
viewDialog state toMsg =
    [ HS.form [ id "cheesto-status-form" ]
        [ fieldset [ class "outer" ]
            [ text "Time Quick Set:"
            , table []
                -- TODO: Convert checkboxes to buttons and connect to date time input
                [ tr []
                    [ td []
                        [ text "10 Minutes"
                        , input [ attribute "data-time-offset" "10", name "quicktime", type_ "radio" ]
                            []
                        ]
                    , td []
                        [ text "20 Minutes"
                        , input [ attribute "data-time-offset" "20", name "quicktime", type_ "radio" ]
                            []
                        ]
                    ]
                , tr []
                    [ td []
                        [ text "30 Minutes"
                        , input [ attribute "data-time-offset" "30", name "quicktime", type_ "radio" ]
                            []
                        ]
                    , td []
                        [ text "40 Minutes"
                        , input [ attribute "data-time-offset" "40", name "quicktime", type_ "radio" ]
                            []
                        ]
                    ]
                , tr []
                    [ td []
                        [ text "50 Minutes"
                        , input [ attribute "data-time-offset" "50", name "quicktime", type_ "radio" ]
                            []
                        ]
                    , td []
                        [ text "1 Hour"
                        , input [ attribute "data-time-offset" "60", name "quicktime", type_ "radio" ]
                            []
                        ]
                    ]
                , tr []
                    [ td []
                        [ text "1 Hour 15 Min."
                        , input [ attribute "data-time-offset" "75", name "quicktime", type_ "radio" ]
                            []
                        ]
                    , td []
                        [ text "1 Hour 30 Min."
                        , input [ attribute "data-time-offset" "90", name "quicktime", type_ "radio" ]
                            []
                        ]
                    ]
                , tr []
                    [ td []
                        [ text "1 Hour 45 Min."
                        , input [ attribute "data-time-offset" "105", name "quicktime", type_ "radio" ]
                            []
                        ]
                    , td []
                        [ text "2 Hours"
                        , input [ attribute "data-time-offset" "120", name "quicktime", type_ "radio" ]
                            []
                        ]
                    ]
                ]
            ]
        , fieldset [ class "outer" ]
            [ fieldset []
                [ label [ for "cheesto-date-pick" ]
                    [ text "Return Time:" ]
                , input
                    -- TODO: Connect to a datetime picker
                    [ id "cheesto-date-pick"
                    , type_ "text"
                    , value state.returntime
                    , onInput (updateTextInput state toMsg ChangeReturnTime)
                    ]
                    []
                ]
            , fieldset []
                [ label [ for "cheesto-message-text" ]
                    [ text "Message:" ]
                , textarea
                    [ attribute "cols" "25"
                    , id "cheesto-message-text"
                    , attribute "rows" "10"
                    , onInput (updateTextInput state toMsg ChangeMessage)
                    ]
                    [ text state.message ]
                ]
            ]
        ]
    ]
