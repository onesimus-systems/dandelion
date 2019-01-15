module Styles exposing (qbFieldSetStyle, qbInputStyle, qbLabelStyle, qbSelectStyle)

import Css exposing (..)


black : Css.Color
black =
    rgb 0 0 0


qbFieldSetStyle : Style
qbFieldSetStyle =
    border (px 0)


qbLabelStyle : Style
qbLabelStyle =
    Css.batch
        [ fontWeight bold
        , display block
        ]


qbInputStyle : Style
qbInputStyle =
    Css.batch
        [ margin2 (px 0) (px 5)
        , color black
        ]


qbSelectStyle : Style
qbSelectStyle =
    color black
