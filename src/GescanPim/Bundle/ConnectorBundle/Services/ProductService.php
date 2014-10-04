<?php
namespace GescanPim\Bundle\ConnectorBundle\Services;

/**
 * Description of ProductItemTransformer
 *
 * @author ecoisne
 */

class ProductService
{
    const MIN_IMAGE_SIZE = 200;

    protected $mediaExtension = array(
        'document'=>array(
            'application/msword'=>'doc',
            'application/pdf'=>'pdf'
         ),
        'image'=>array(
            'image/gif'=>'gif',
            'image/jpeg'=>'jpg',
            'image/png'=>'png',
            'image/x-ms-bmp'=>'bmp'
        )
    );

    protected $authorizeAccronym = array(
        'led'=>'LED'
    );

    protected $authorizeAccronym = array(
        'led'=>'LED'
    );
    protected $sxTextAbreviationMapping = array(
        '/( ACCY )/i'=>' Accessory ' ,  '/( #LB )/i'=>' Lamps per Ballast ' , '/( ACCU )/i'=>' Actuator ' ,
        '/( ACR )/i'=>' Acrylic '    ,  '/( ACT )/i'=>' Action '            , '/( ADAPT )/i'=>' Adaptor ' ,
        '/( ADJ )/i'=>' Adjustable ' ,  '/( ADVB )/i'=>' Advance Ballast '  , '/( AERO )/i'=>' Aerosol ' ,
        '/( AF )/i'=>' Arc Fault '   ,  '/( ALM )/i'=>' Almond '            , '/( ALUM )/i'=>' Aluminum ' ,
        '/( AMB )/i'=>' Amber '      ,  '/( ANG )/i'=>' Angle '             , '/( ANT )/i'=>' Antique Brass ' ,
        '/( APER )/i'=>' Aperture '  ,  '/( APPL )/i'=>' Appliance '        , '/( ARM )/i'=>' Armored ' ,
        '/( ASSY )/i'=>' Assembly '  ,  '/( AUTO )/i'=>' Automatic '        , '/( AUX )/i'=>' Auxiliary ' ,
        '/( AWG )/i'=>' Gauge '      ,  '/( B&S )/i'=>' Back & Side Wired ' , '/( BAL )/i'=>' Ballast ' ,
        '/( BC )/i'=>' Blister Card ',  '/( BCH )/i'=>' Brushed Chrome '    , '/( BDP )/i'=>' Ballast Disconnect Plug ' ,
        '/( BEI )/i'=>' Beige '      ,  '/( BEV )/i'=>' Beveled '           , '/( BLK )/i'=>' Black ' ,
        '/( BLU )/i'=>' Blue '       ,  '/( BP )/i'=>' Back Plate '         , '/( BPEW )/i'=>' Brushed Pewter ' ,
        '/( BRA )/i'=>' Brass '      ,  '/( BRO )/i'=>' Brown '             , '/( BRU )/i'=>' Brushed ' ,
        '/( BRZ )/i'=>' Bronze '     ,  '/( BST )/i'=>' Brushed Steel '     , '/( BU )/i'=>' Base Up ' ,
        '/( BUSH )/i'=>' Bushing '   ,  '/( BUT )/i'=>' Button '            , '/( C )/i'=>' Celsius ' ,
        '/( C&C )/i'=>' Core & Coil ',  '/( CAND )/i'=>' Candelabra '       , '/( CANP )/i'=>' Canopy ' ,
        '/( CAP )/i'=>' Capacity '   ,  '/( CAT )/i'=>' Category '          , '/( CCT )/i'=>' Circuit ' ,
        '/( CFL )/i'=>' Compact Fluorescent '   , '/( CG )/i'=>' Covered Glass '    , '/( CH )/i'=>' Channel ' ,
        '/( CHAND )/i'=>' Chandelier '          , '/( ci )/i'=>' Cubic Inch '       , '/( CIRC )/i'=>' Circline ' ,
        '/( CL )/i'=>' Class '                  , '/( CLAM )/i'=>' Clamshell '      , '/( CLG )/i'=>' Ceiling ' ,
        '/( CLR )/i'=>' Clear '                 , '/( COAT )/i'=>' Coating '        , '/( COLD )/i'=>' Cold Start ' ,
        '/( COM )/i'=>' Communication '         , '/( COMBO )/i'=>' Combination '   , '/( COMM )/i'=>' Commercial ' ,
        '/( COMPL )/i'=>' Complete '            , '/( COMPRESS )/i'=>' Compression ', '/( CONDUCT )/i'=>' Conductor ' ,
        '/( CONN )/i'=>' Connector '            , '/( CONS )/i'=>' Construction '   , '/( CONT )/i'=>' Contactor ' ,
        '/( CONTIN )/i'=>' Continuity '         , '/( CONVERT )/i'=>' Convertible ' , '/( CORR )/i'=>' Corrosive ' ,
        '/( COUP )/i'=>' Coupling '             , '/( CTN )/i'=>' Carton '          , '/( CTRL )/i'=>' Control ' ,
        '/( CU )/i'=>' Copper '                 , '/( CVR )/i'=>' Cover '           , '/( CW )/i'=>' Cool White ' ,
        '/( CYL )/i'=>' Cylinder '              , '/( DBL )/i'=>' Double '          , '/( DC )/i'=>' Die-Cast ' ,
        '/( DEC )/i'=>' Decorator '             , '/( DEG )/i'=>' Degree '          , '/( DETECT )/i'=>' Detector ' ,
        '/( DEV )/i'=>' Device '                , '/( DIA )/i'=>' Diameter '        , '/( DIFF )/i'=>' Differential ' ,
        '/( DIGI )/i'=>' Digital '              , '/( DIM )/i'=>' Dimmer '          , '/( DISC )/i'=>' Disconnect ' ,
        '/( DIST )/i'=>' Distribution '         , '/( DIV )/i'=>' Division '        , '/( DL )/i'=>' Day Light ' ,
        '/( DP )/i'=>' Definite Purpose '       , '/( DPDT )/i'=>' Double Pull Double Throw '   , '/( DR )/i'=>' Door ' ,
        '/( DT )/i'=>' Double Throw '           , '/( DUP )/i'=>' Duplex '                      , '/( E STOP )/i'=>' Emergency Stop ' ,
        '/( ECONO )/i'=>' Economy '             , '/( EHU )/i'=>' Extra Heavy Use '             , '/( ELEC )/i'=>' Electrical ' ,
        '/( ELLIP )/i'=>' Elliptical '          , '/( ELV )/i'=>' Electronic Low Voltage '      , '/( EME )/i'=>' Emerald ' ,
        '/( EMERG )/i'=>' Emergency '           , '/( ENCAP )/i'=>' Encapsulated '              , '/( ENCL )/i'=>' Enclosure ' ,
        '/( ENG )/i'=>' English '               , '/( ESB )/i'=>' Energy Saving Ballast '       , '/( EXPAN )/i'=>' Expansion ' ,
        '/( EXT )/i'=>' Extension '             , '/( F )/i'=>' Fahrenheit '                    , '/( F\/F )/i'=>' Female to Female ' ,
        '/( FDR )/i'=>' Feeder '                , '/( FE )/i'=>' Fire Extinguisher '            , '/( FITT )/i'=>' Fitting ' ,
        '/( FIXT )/i'=>' Fixture '              , '/( FL )/i'=>' Flood '                        , '/( FLA )/i'=>' Full Load Ampere ' ,
        '/( FLGD )/i'=>' Flange '               , '/( FLUOR )/i'=>' Fluorescent '               , '/( FM )/i'=>' Form ' ,
        '/( FP )/i'=>' Face Plate '             , '/( FREQ )/i'=>' Frequency '                  , '/( FRST )/i'=>' Frost ' ,
        '/( FRST )/i'=>' Frosted '              , '/( FRT )/i'=>' Front '                       , '/( ft )/i'=>' Feet ' ,
        '/( FV )/i'=>' Full Voltage '           , '/( FVNR )/i'=>' Full Voltage Non Reversing ' , '/( FWD )/i'=>' Forward ' ,
        '/( G )/i'=>' Gang '                    , '/( G&G )/i'=>' Globe & Guard '               , '/( gal )/i'=>' Gallon ' ,
        '/( GALV )/i'=>' Galvanized '           , '/( GASK )/i'=>' Gasket '                     , '/( GF )/i'=>' Ground Fault ' ,
        '/( GFI )/i'=>' Ground Fault Indicator ', '/( GLD )/i'=>' Gold '                        , '/( GP )/i'=>' General Purpose ' ,
        '/( GRD )/i'=>' Grade '                 , '/( GRE )/i'=>' Green '                       , '/( GRND )/i'=>' Ground '               ,
        '/( GRY )/i'=>' Gray '                  , '/( H )/i'=>' Hole '                          , '/( HAL )/i'=>' Halogen ' ,
        '/( HAZ )/i'=>' Hazard '                , '/( HAZ )/i'=>' Haze '                        , '/( HC )/i'=>' Hook & Cord ' ,
        '/( HD )/i'=>' Heavy Duty '             ,
        '/( HI )/i'=>' High ' ,
        '/( HIBAY )/i'=>' High Bay ' ,
        '/( HID )/i'=>' High Intensity Discharge ' ,
        '/( HO )/i'=>' High Output ' ,
        '/( HORI )/i'=>' Horizontal ' ,
        '/( HOSP )/i'=>' Hospital ' ,
        '/( HP )/i'=>' Horsepower ' ,
        '/( HPF )/i'=>' High Power Factor ' ,
        '/( HPS )/i'=>' High Pressure Sodium ' ,
        '/( HSG )/i'=>' Housing ' ,
        '/( HU )/i'=>' Hard Use ' ,
        '/( HWY )/i'=>' Highway ' ,
        '/( IDENT )/i'=>' Identify ' ,
        '/( IG )/i'=>' Isolated Ground ' ,
        '/( ILLUM )/i'=>' Illuminate ' ,
        '/( in )/i'=>' Inch ' ,
        '/( INC )/i'=>' Incandescent ' ,
        '/( IND )/i'=>' Industrial ' ,
        '/( INDIC )/i'=>' Indicator ' ,
        '/( INDUCT )/i'=>' Inductive ' ,
        '/( INS )/i'=>' Inside ' ,
        '/( INST )/i'=>' Instrument ' ,
        '/( INSTANT )/i'=>' Instant Start ' ,
        '/( INSUL )/i'=>' Insulator ' ,
        '/( INT )/i'=>' Interior ' ,
        '/( INTER )/i'=>' Interlock ' ,
        '/( INTERMED )/i'=>' Intermediate ' ,
        '/( IR )/i'=>' Infra Red ' ,
        '/( ISO )/i'=>' Isolator ' ,
        '/( IV )/i'=>' Ivory ' ,
        '/( JB )/i'=>' Junction Box ' ,
        '/( K12 )/i'=>' Prismatic ' ,
        '/( KO )/i'=>' Knock Out ' ,
        '/( LA )/i'=>' Light Almond ' ,
        '/( lb )/i'=>' Pound ' ,
        '/( LBLU )/i'=>' Light Blue ' ,
        '/( LD )/i'=>' Light Duty ' ,
        '/( LDCTR )/i'=>' Load Center ' ,
        '/( LEV )/i'=>' Lever ' ,
        '/( LGRE )/i'=>' Light Green ' ,
        '/( LGRY )/i'=>' Light Gray ' ,
        '/( LGTH )/i'=>' Length ' ,
        '/( LOBAY )/i'=>' Low Bay ' ,
        '/( LOC )/i'=>' Location ' ,
        '/( L-OFF )/i'=>' Lift Off ' ,
        '/( LPB )/i'=>' Low Power Ballast ' ,
        '/( LPF )/i'=>' Low Power Factor ' ,
        '/( LPRO )/i'=>' Low Profile ' ,
        '/( LPS )/i'=>' Low Pressure Sodium ' ,
        '/( LRG )/i'=>' Large ' ,
        '/( LT )/i'=>' Liquidtight ' ,
        '/( ltr )/i'=>' Litre ' ,
        '/( LUBE )/i'=>' Lubricant ' ,
        '/( LV )/i'=>' Low Voltage ' ,
        '/( M\/F )/i'=>' Male/Female ' ,
        '/( MAG )/i'=>' Magnetic ' ,
        '/( MAIN )/i'=>' Maintenance ' ,
        '/( MAN )/i'=>' Manual ' ,
        '/( MAX )/i'=>' Maximum ' ,
        '/( mb )/i'=>' Megabyte ' ,
        '/( MBEAM )/i'=>' Multi Beam ' ,
        '/( MD )/i'=>' Medium Duty ' ,
        '/( MECH )/i'=>' Mechanical ' ,
        '/( MED )/i'=>' Medium ' ,
        '/( MGMT )/i'=>' Management ' ,
        '/( MH )/i'=>' Metal Halide ' ,
        '/( MIN )/i'=>' Minute ' ,
        '/( MINI )/i'=>' Miniature ' ,
        '/( ml )/i'=>' Millilitre ' ,
        '/( MLO )/i'=>' Main Lugs Only ' ,
        '/( MLV )/i'=>' Magnetic Low Voltage ' ,
        '/( MNT )/i'=>' Mount ' ,
        '/( MOD )/i'=>' Module ' ,
        '/( MOG )/i'=>' Mogul ' ,
        '/( MOM )/i'=>' Momentary ' ,
        '/( MSTR )/i'=>' Master ' ,
        '/( mtr )/i'=>' Meter ' ,
        '/( MUSH )/i'=>' Mushroom ' ,
        '/( MVolt )/i'=>' Multivolt ' ,
        '/( NAR )/i'=>' Narrow ' ,
        '/( NAT )/i'=>' Natural ' ,
        '/( NC )/i'=>' Normally Closed ' ,
        '/( NEUT )/i'=>' Neutral ' ,
        '/( NF )/i'=>' Non-Fused ' ,
        '/( NFL )/i'=>' Narrow Flood ' ,
        '/( NKL )/i'=>' Nickel ' ,
        '/( NM )/i'=>' Non Metallic ' ,
        '/( NO )/i'=>' Normally Open ' ,
        '/( NON-CYC )/i'=>' Non-Cycling ' ,
        '/( NPT )/i'=>' National Pipe Thread ' ,
        '/( NSP )/i'=>' Narrow Spot ' ,
        '/( OCCUP )/i'=>' Occupancy ' ,
        '/( OCT )/i'=>' Octagonal ' ,
        '/( OD )/i'=>' Outside Diameter ' ,
        '/( OH )/i'=>' Overhead ' ,
        '/( OL )/i'=>' Over Load ' ,
        '/( OLI )/i'=>' Olive ' ,
        '/( OPA )/i'=>' Opal ' ,
        '/( OPER )/i'=>' Operator ' ,
        '/( ORA )/i'=>' Orange ' ,
        '/( OS )/i'=>' Oversized ' ,
        '/( OUT )/i'=>' Outside ' ,
        '/( OVTR )/i'=>' Over Travel ' ,
        '/( oz )/i'=>' Ounce ' ,
        '/( P )/i'=>' Pole ' ,
        '/( P\/B )/i'=>' Push Button ' ,
        '/( P\/L )/i'=>' Pilot Light ' ,
        '/( PARA )/i'=>' Parabolic ' ,
        '/( PC )/i'=>' Photo Cell ' ,
        '/( PEND )/i'=>' Pendant ' ,
        '/( PEW )/i'=>' Pewter ' ,
        '/( PH )/i'=>' Phase ' ,
        '/( PIN&SLEEVE )/i'=>' Powertite ' ,
        '/( PLUNG )/i'=>' Plunger ' ,
        '/( PNK )/i'=>' Pink ' ,
        '/( POB )/i'=>' Polished Brass ' ,
        '/( POC )/i'=>' Polished Chrome ' ,
        '/( PORC )/i'=>' Porcelain ' ,
        '/( POS )/i'=>' Position ' ,
        //'/( POT )/i'=>' Potentiometer ' ,
        '/( PR )/i'=>' Pair ' ,
        '/( PRECONST )/i'=>' Preconstruction ' ,
        '/( PREM )/i'=>' Premium ' ,
        '/( PRESS )/i'=>' Pressure ' ,
        '/( PRESSP )/i'=>' Pressure Plate ' ,
        '/( PRIM )/i'=>' Primary ' ,
        '/( PROG )/i'=>' Programmable ' ,
        '/( PSET )/i'=>' Pre-set ' ,
        '/( pt )/i'=>' Pint ' ,
        '/( PULSE )/i'=>' Pulse Start ' ,
        '/( PUR )/i'=>' Purple ' ,
        '/( Q&S )/i'=>' Quick & Side Wired ' ,
        '/( QCONN )/i'=>' Quick Connect ' ,
        '/( qrt )/i'=>' Quart ' ,
        '/( QT )/i'=>' Quad Tap ' ,
        '/( QTZ )/i'=>' Quartz ' ,
        '/( RAPID )/i'=>' Rapid Start ' ,
        '/( RATCH )/i'=>' Ratcheting ' ,
        '/( RCS )/i'=>' Recessed ' ,
        '/( REC )/i'=>' Receptacle ' ,
        '/( RECIP )/i'=>' Reciprocating ' ,
        '/( RECTANG )/i'=>' Rectangular ' ,
        '/( RED )/i'=>' Red ' ,
        '/( REDU )/i'=>' Reducer ' ,
        '/( REFL )/i'=>' Reflector ' ,
        '/( REG )/i'=>' Regulator ' ,
        '/( REPL )/i'=>' Replacement ' ,
        '/( RESI )/i'=>' Residential ' ,
        '/( RESIST )/i'=>' Resistor ' ,
        '/( RET )/i'=>' Return ' ,
        '/( REV )/i'=>' Reverse ' ,
        '/( RGS )/i'=>' Regressed ' ,
        '/( RMS )/i'=>' True RMS ' ,
        '/( RND )/i'=>' Round ' ,
        '/( ROBBIE )/i'=>' Robertson ' ,
        '/( ROL )/i'=>' Roller ' ,
        '/( ROT )/i'=>' Rotary ' ,
        '/( RS )/i'=>' Rough Service ' ,
        '/( RSTK )/i'=>' Restrike ' ,
        '/( RT )/i'=>' Raintight ' ,
        '/( RUB )/i'=>' Ruby ' ,
        '/( SB )/i'=>' Straight Blade ' ,
        '/( SBR )/i'=>' Satin Brass ' ,
        '/( SC )/i'=>' Self Centering ' ,
        '/( SCH )/i'=>' Satin Chrome ' ,
        '/( SEC )/i'=>' Secondary ' ,
        '/( SECT )/i'=>' Sectional ' ,
        '/( SECUR )/i'=>' Security ' ,
        '/( SEL )/i'=>' Selector ' ,
        '/( SER )/i'=>' Series ' ,
        '/( SERV )/i'=>' Service ' ,
        '/( SGL )/i'=>' Single ' ,
        '/( SHAL )/i'=>' Shallow ' ,
        '/( SHLD )/i'=>' Shielded ' ,
        //'/( SIDE )/i'=>' Sidemount ' ,
        '/( SLV )/i'=>' Silver ' ,
        '/( SM )/i'=>' Small ' ,
        '/( SNKL )/i'=>' Satin Nickel ' ,
        '/( SOCK )/i'=>' Socket ' ,
        '/( SOL )/i'=>' Solid ' ,
        '/( SP )/i'=>' Spot ' ,
        '/( SPKR )/i'=>' Speaker ' ,
        '/( SPR )/i'=>' Spring ' ,
        '/( SPST )/i'=>' Single Pull Single Throw ' ,
        '/( SQ )/i'=>' Square ' ,
        '/( sqft )/i'=>' Square Foot ' ,
        '/( SR )/i'=>' Strain Relief ' ,
        '/( SS )/i'=>' Stainless Steel ' ,
        '/( ST )/i'=>' Single Throw ' ,
        '/( STAT )/i'=>' Thermostat ' ,
        '/( STD )/i'=>' Standard ' ,
        '/( STL )/i'=>' Steel ' ,
        '/( STN )/i'=>' Station ' ,
        '/( STR )/i'=>' Stranded ' ,
        '/( STR BLD )/i'=>' Straight Blade ' ,
        '/( STY )/i'=>' Styrene ' ,
        '/( SUPP )/i'=>' Suppression ' ,
        '/( SURF )/i'=>' Surface ' ,
        '/( SUSP )/i'=>' Suspended ' ,
        '/( T\/L )/i'=>' Turn Lok ' ,
        '/( TAND )/i'=>' Tandem ' ,
        '/( TAPER )/i'=>' Tapered ' ,
        '/( T-BAR )/i'=>' Troffer ' ,
        '/( TEL )/i'=>' Telephone ' ,
        '/( TELECOM )/i'=>' Telecommunication ' ,
        '/( TEMP )/i'=>' Temperature ' ,
        '/( TERM )/i'=>' Terminal ' ,
        '/( TOG )/i'=>' Toggle ' ,
        '/( TT )/i'=>' Tri-Tap ' ,
        '/( TXFMR )/i'=>' Transformer ' ,
        '/( UC )/i'=>' Under Cabinet ' ,
        '/( UG )/i'=>' Underground ' ,
        '/( UNIV )/i'=>' Universal ' ,
        '/( UNSH )/i'=>' Unshielded ' ,
        '/( USONIC )/i'=>' Ultrasonic ' ,
        '/( UTIL )/i'=>' Utility ' ,
        '/( UV )/i'=>' Under Voltage ' ,
        '/( VAC )/i'=>' VoltAC ' ,
        '/( VAR )/i'=>' Variable ' ,
        '/( VDC )/i'=>' VoltDC ' ,
        '/( VENT )/i'=>' Ventilated ' ,
        '/( VERT )/i'=>' Vertical ' ,
        '/( VHO )/i'=>' Very High Output ' ,
        '/( VIB )/i'=>' Vibration ' ,
        '/( VIO )/i'=>' Violet ' ,
        '/( WFL )/i'=>' Wide Flood ' ,
        '/( WHI )/i'=>' White ' ,
        '/( WOB )/i'=>' Wobble ' ,
        '/( WP )/i'=>' Weatherproof ' ,
        '/( WRM IV )/i'=>' Wiremold Ivory ' ,
        '/( WSP )/i'=>' Wide Spot ' ,
        '/( WT )/i'=>' Watertight ' ,
        '/( WW )/i'=>' Warm White ' ,
        '/( XHD )/i'=>' Extra Heavy Duty ' ,
        '/( XP )/i'=>' Explosion Proof ' ,
        '/( YEL )/i'=>' Yellow ' ,




        /************AUTHORIZE ACRONYM************************************/
        '/( LED )/i' => ' LED ',

    );


    protected $sxCategoryMapping = array(
        '14'=>'lamps_ballasts',
        '15'=>'lamps_ballasts','15AC'=>'lamps_ballasts_ballasts', '15BE' =>'lamps_ballasts_ballasts', '15BM'=>'lamps_ballasts_ballasts',
        '14'=>'lamps_ballasts','14EI'=>'lamps_ballasts_incandescent_lamps','14EF'=>'lamps_ballasts_ballast_lamps','14EA'=>'lamps_ballasts_halogen_lamps',
        '14ED'=>'lamps_ballasts_led_lamps', '13ED'=>'lamps_ballasts_led_lamps',
        '14EH'=>'lamps_ballasts_hid_lamps',
        '18'=>'tools','09'=>'tools','10'=>'tools','08'=>'tools',
        '18LU'=>'tools_chemicals', '18PE'=>'tools_chemicals',
        '09DZ'=>'tools_data_tools', '09DH'=>'tools_data_tools',
        '09OM'=>'tools_hand_tools', '910'=>'tools_hand_tools',
        '09OE'=>'tools_power_tools',
        '10RU'=>'tools_insulation', '10SI'=>'tools_insulation', '10TU'=>'tools_insulation',
        '09AC'=>'tools_straps_staples',
        '09TY'=>'tools_shop_supplies', '09VE'=>'tools_shop_supplies', '09MA'=>'tools_shop_supplies', '09BA'=>'tools_shop_supplies', '09AO'=>'tools_shop_supplies',
        '05'=>'switches_wiring_devices',
        '05CO'=>'switches_wiring_devices_plugs_receptacles',
        '05PL'=>'switches_wiring_devices_wall_plates',
        '05GR'=>'switches_wiring_devices_dimmers',
        '05RE'=>'switches_wiring_devices_gfci',
        '01'=>'conduit_raceways','02'=>'conduit_raceways',
        '01AF'=>'conduit_raceways_conduit', '01CA'=>'conduit_raceways_conduit', '01CE'=>'conduit_raceways_conduit', '01CP'=>'conduit_raceways_conduit', '01CR'=>'conduit_raceways_conduit', '01FA'=>'conduit_raceways_conduit', '01FE'=>'conduit_raceways_conduit', '01HQ'=>'conduit_raceways_conduit', '01II'=>'conduit_raceways_conduit', '01NM'=>'conduit_raceways_conduit',
        '01DF'=>'conduit_raceways_raceways', '02CA'=>'conduit_raceways_raceways', '02GO'=>'conduit_raceways_raceways', '03RW'=>'conduit_raceways_raceways',
        '02CO'=>'conduit_raceways_accessories',
        '02KG'=>'conduit_raceways_connectors', '02CT'=>'conduit_raceways_connectors', '02R2'=>'conduit_raceways_connectors', '02RA'=>'conduit_raceways_connectors', '02RC'=>'conduit_raceways_connectors', '02RE'=>'conduit_raceways_connectors', '02RF'=>'conduit_raceways_connectors', '02RP'=>'conduit_raceways_connectors', '02RR'=>'conduit_raceways_connectors', '02RS'=>'conduit_raceways_connectors', '02RT'=>'conduit_raceways_connectors',
        '04'=>'wire_cable',
        '04CA'=>'wire_cable_armoured', '04CF'=>'wire_cable_armoured', '04IK'=>'wire_cable_armoured',
        '04IU'=>'wire_cable_low_voltage',
        '04IR'=>'wire_cable_specialty',
        '04CE'=>'wire_cable_bare',
        '04IS'=>'wire_cable_industrial', '04IQ'=>'wire_cable_industrial',
        '04IP'=>'wire_cable_portable',
        '04RN'=>'wire_cable_reels', '04RR'=>'wire_cable_reels',
        '04DU'=>'wire_cable_data_low_voltage', '04IT'=>'wire_cable_data_low_voltage',
        '04CB'=>'wire_cable_common','04CC'=>'wire_cable_common','04CD'=>'wire_cable_common','04CG'=>'wire_cable_common','04CH'=>'wire_cable_common','04CI'=>'wire_cable_common','04CJ'=>'wire_cable_common','04CM'=>'wire_cable_common',
        '22'=>'datacomm',
        '04DU'=>'datacomm_low_voltage_cable', '04IT'=>'datacomm_low_voltage_cable',
        '07'=>'power_distribution',
        '07SC'=>'power_distribution_meter_sockets_accessories',
        '07PD'=>'power_distribution_panels','07PT'=>'power_distribution_panels',
        '07IN'=>'power_distribution_switches',
        '07TC'=>'power_distribution_transformers','07TD'=>'power_distribution_transformers',
        '06'=>'fire_security',
        '12EU'=>'home_automation_controls_emergency_lighting', '13EU'=>'home_automation_controls_emergency_lighting',
        '06SI'=>'home_automation_controls_alarm_signals','06SV'=>'home_automation_controls_alarm_signals',
        '19'=>'home_automation_controls',
        '05DA'=>'home_automation_controls_video',
        '05DB'=>'home_automation_controls_accessories',
        '19AP'=>'home_automation_controls_audio',
        '05DD'=>'home_automation_controls_controls',
        '11'=>'heating_ventilation', '11AC'=>'heating_ventilation',
        '11CP'=>'heating_ventilation_heaters','11CI'=>'heating_ventilation_heaters', '11CH'=>'heating_ventilation_heaters', '11CE'=>'heating_ventilation_heaters', '11CF'=>'heating_ventilation_heaters',
        '1111'=>'heating_ventilation_surface_heating', '11CC'=>'heating_ventilation_surface_heating',
        '11TR'=>'heating_ventilation_thermostats_controls',
        '11VM'=>'heating_ventilation_fans', '11VP'=>'heating_ventilation_fans',
        '11VA'=>'heating_ventilation_central_vacuum',
        '11VB'=>'heating_ventilation_ventillation', '11VH'=>'heating_ventilation_ventillation',
        '03BB'=>'boxes_enclosures_pvc_boxes',
        '03CP'=>'boxes_enclosures_plates',
        '03AC'=>'boxes_enclosures_accessories',
        '03BP'=>'boxes_enclosures_floor_boxes',
        '03BJ'=>'boxes_enclosures_junction_boxes', '03CB'=>'boxes_enclosures_junction_boxes',
        '03CA'=>'boxes_enclosures_enclosures', '03CM'=>'boxes_enclosures_enclosures', '03DE'=>'boxes_enclosures_enclosures',
        '03BI'=>'boxes_enclosures_metal_boxes', '03BM'=>'boxes_enclosures_metal_boxes', '03BS'=>'boxes_enclosures_metal_boxes', '03BA'=>'boxes_enclosures_metal_boxes',
        '12'=>'lighting', '13'=>'lighting', '13EA'=>'lighting', '12EI'=>'lighting', '12PC'=>'lighting',
        '12EF'=>'lighting_troffers', '13EF'=>'lighting_troffers',
        '13EH'=>'lighting_recessed',
        '12EH'=>'lighting_wallpacks',
        '13EI'=>'lighting_recessed_trims',
        '12ED'=>'lighting_outdoor',
        '16'=>'industrial_automation_controls',
        '16AP'=>'industrial_automation_controls_automation_plc', '05DI'=>'industrial_automation_controls_automation_plc',
        '16AD'=>'industrial_automation_controls_automation_discrete',
        '16AZ'=>'industrial_automation_controls_automation_others',
        '16AI'=>'industrial_automation_controls_automation_interface',
        '16CD'=>'industrial_automation_controls_control_detection',
        '16CI'=>'industrial_automation_controls_control_iec',
        '16CN'=>'industrial_automation_controls_control_nema', '16CS'=>'industrial_automation_controls_control_safety', '16CZ'=>'industrial_automation_controls_control_others',
        '16MC'=>'industrial_automation_controls_motor_control_mmc', '16MO'=>'industrial_automation_controls_motor_electrical', '16OI'=>'industrial_automation_controls_operator_interface',
        '16SS'=>'industrial_automation_controls_service_software', '16SW'=>'industrial_automation_controls_automation_software', '17'=>'connectors_terminals',
        '17GR'=>'connectors_terminals_fittings', '17MA'=>'connectors_terminals_marretts', '17CO'=>'connectors_terminals_connectors',
        '20'=>'fuses_breakers', '07DI'=>'fuses_breakers_breakers', '20AF'=>'fuses_breakers_fuse_accessories',
        '20PF'=>'fuses_breakers_fuse_blocks_holders', '20BF'=>'fuses_breakers_fuse_blocks_holders', '20FU'=>'fuses_breakers_fuses',
    );



    public function __construct($upload_dir) {
        $this->upload_dir = $upload_dir;
    }

    public function setEntityManager($em){
        $this->em = $em;
        $this->categoryManager =  $this->em->getRepository('PimCatalogBundle:Category');
        $this->attributeRepository =  $this->em->getRepository('PimCatalogBundle:Attribute');
        $this->familyRepository =  $this->em->getRepository('PimCatalogBundle:Family');
        $this->mappingCodeRepository  = $this->em->getRepository('GescanPimConnectorBundle:MappingCode');
        return  $this;
    }

    public function getScopableProductAttribute ($Product) {
        $return = array();
        foreach ($Product->getAttributes() as $attribute){

            if ($attribute->isScopable()){
                $return[$attribute->getCode()]=$attribute;
            }
        }
        //var_dump(count($return));
        return $return;
    }

    protected $urlkeyAttribute = null;

    protected function getUrlKeyAttribute(){
        if (!$this->urlkeyAttribute){
            $this->urlkeyAttribute = $this->attributeRepository->findByReference('_url_key');
        }
        return $this->urlkeyAttribute;
    }

    protected function getAttribute($code){
        return $this->attributeRepository->findByReference($code);
    }

    protected function updateProductAttribute($attributeCode, $value)
    {
        if ( !$value ) return $this;
        /* Check the attribute */
        $Attribute = $this->getAttribute($attributeCode);
        if ($Attribute == null) return $this;

        $scope = $Attribute->isScopable()?$this->channel : null;

        /** Specific treatement for attribute of type media **/
        if ($Attribute->getBackendType()=='media') {
            $media = false;
            if ($this->product->getValue($Attribute->getCode(),null,$scope)&&
               $this->product->getValue($Attribute->getCode(), null, $scope)->getMedia()
		    ){
               $media = $this->product->getValue($Attribute->getCode(), null, $scope)->getMedia();
            } else {
                $media = new Media();
            }
            $file = $this->getFile($value);
            if ($file) {
                $media->setFile($file);
                $value = $media;
            } else {
                /** file does not exist **/
                return $this;
            }
        }

        if (!$this->product->getValue($Attribute->getCode(),null,$scope)) {
            /** Creation of the attribute value **/
            $Value   = $this->productManager->createProductValue();
            $Value->setAttribute($Attribute);
            $Value->setScope($scope);
            $Value->setData($value);
            $this->product->addValue($Value);
        } else {
            $this->product->getValue($Attribute->getCode(), null, $scope)->setData($value);
        }

        return $this;
    }
}
