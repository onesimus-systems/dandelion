module Styles exposing (qbFieldSetStyle, qbInputStyle, qbLabelStyle)

import Css exposing (..)


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
        , color (rgb 0 0 0)
        ]
