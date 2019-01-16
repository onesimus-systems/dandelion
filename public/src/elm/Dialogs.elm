module Dialogs exposing
    ( AlertConfig
    , DialogConfig
    , alert
    , alertWithConfig
    , confirm
    , defaultAlertConfig
    , defaultConfirmConfig
    , defaultDialogConfig
    , dialog
    , dialogWithConfig
    , overlay
    , overlayStyled
    )

import Css exposing (..)
import Html
import Html.Styled exposing (..)
import Html.Styled.Attributes exposing (class, css, id)
import Html.Styled.Events exposing (onClick)
import Styles exposing (..)


type alias DialogConfig =
    { title : String
    , okBtnText : String
    , cancelBtnText : String
    }


defaultConfirmConfig : DialogConfig
defaultConfirmConfig =
    { title = "Confirm"
    , okBtnText = "Yes"
    , cancelBtnText = "Cancel"
    }


defaultDialogConfig : DialogConfig
defaultDialogConfig =
    { title = "Dialog"
    , okBtnText = "Ok"
    , cancelBtnText = "Cancel"
    }


confirm : String -> (Bool -> msg) -> Html msg
confirm prompt toMsg =
    dialogWithConfig defaultConfirmConfig [ text prompt ] toMsg


dialog : List (Html msg) -> (Bool -> msg) -> Html msg
dialog prompt toMsg =
    dialogWithConfig defaultDialogConfig prompt toMsg


dialogWithConfig : DialogConfig -> List (Html msg) -> (Bool -> msg) -> Html msg
dialogWithConfig config prompt toMsg =
    div
        [ id "dialog", css [ dialogStyle ] ]
        [ div [ id "dialog-header", css [ dialogHeaderStyle ] ]
            [ span [ class "dialog-box-title" ] [ text config.title ] ]
        , div [ css [ dialogBodyStyle ] ]
            prompt
        , div [ css [ dialogFooterStyle ] ]
            [ button
                [ class "dialog-box-ok-btn"
                , css [ dialogFooterBtnStyle ]
                , onClick (toMsg True)
                ]
                [ text config.okBtnText ]
            , button
                [ class "dialog-box-cancel-btn"
                , css [ dialogFooterBtnStyle ]
                , onClick (toMsg False)
                ]
                [ text config.cancelBtnText ]
            ]
        ]


type alias AlertConfig =
    { title : String
    , okBtnText : String
    }


defaultAlertConfig : AlertConfig
defaultAlertConfig =
    { title = "Message"
    , okBtnText = "Ok"
    }


alert : List (Html msg) -> msg -> Html msg
alert prompt toMsg =
    alertWithConfig defaultAlertConfig prompt toMsg


alertWithConfig : AlertConfig -> List (Html msg) -> msg -> Html msg
alertWithConfig config prompt toMsg =
    div
        [ css [ dialogStyle ] ]
        [ div [ css [ dialogHeaderStyle ] ]
            [ span [ class "dialog-box-title" ] [ text config.title ] ]
        , div [ css [ dialogBodyStyle ] ]
            prompt
        , div [ css [ dialogFooterStyle ] ]
            [ button
                [ class "dialog-box-ok-btn"
                , css [ dialogFooterBtnStyle ]
                , onClick toMsg
                ]
                [ text config.okBtnText ]
            ]
        ]


overlay : Html.Html msg
overlay =
    toUnstyled overlayStyled


overlayStyled : Html msg
overlayStyled =
    div
        [ css
            [ position absolute
            , top (px 0)
            , left (px 0)
            , zIndex (int 10)
            , opacity (num 0.2)
            , backgroundColor (rgb 0 0 0)
            , width (pct 100)
            , height (pct 100)
            ]
        ]
        []
