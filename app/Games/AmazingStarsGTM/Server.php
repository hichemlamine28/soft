<?php 
namespace VanguardLTE\Games\AmazingStarsGTM
{
    include('CheckReels.php');
    class Server
    {
        public function get($request, $game)
        {/*
            if( config('LicenseDK.APL_INCLUDE_KEY_CONFIG') != 'wi9qydosuimsnls5zoe5q298evkhim0ughx1w16qybs2fhlcpn' ) 
            {
                return false;
            }
            if( md5_file(base_path() . '/config/LicenseDK.php') != '27f30d89977203af2f6822e48707425d' ) 
            {
                return false;
            }
            if( md5_file(base_path() . '/app/Lib/LicenseDK.php') != '22dde427cc10243ac0c7a3a625518e6f' ) 
            {
                return false;
            }
            $checked = new \VanguardLTE\Lib\LicenseDK();
            $license_notifications_array = $checked->aplVerifyLicenseDK(null, 0);
            if( $license_notifications_array['notification_case'] != 'notification_license_ok' ) 
            {
                $response = '{"responseEvent":"error","responseType":"error","serverResponse":"Error LicenseDK"}';
                exit( $response );
            }
*/            $response = '';
            \DB::beginTransaction();
            $userId = \Auth::id();
            if( $userId == null ) 
            {
                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid login"}';
                exit( $response );
            }
            $slotSettings = new SlotSettings($game, $userId);
            $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22 = json_decode(trim(file_get_contents('php://input')), true);
            if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'update' ) 
            {
                $response = '{"responseEvent":"error","responseType":"' . $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] . '","serverResponse":"' . $slotSettings->GetBalance() . '"}';
                exit( $response );
            }
            if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'bet' || $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' || $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'respin' ) 
            {
                if( !in_array($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'], $slotSettings->gameLine) || !in_array($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'], $slotSettings->Bet) ) 
                {
                    $response = '{"responseEvent":"error","responseType":"' . $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] . '","serverResponse":"invalid bet state"}';
                    exit( $response );
                }
                if( $slotSettings->GetBalance() < ($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet']) && $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'bet' ) 
                {
                    $response = '{"responseEvent":"error","responseType":"' . $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] . '","serverResponse":"invalid balance"}';
                    exit( $response );
                }
                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' ) 
                {
                    $response = '{"responseEvent":"error","responseType":"' . $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] . '","serverResponse":"invalid bonus state"}';
                    exit( $response );
                }
            }
            else if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'slotGamble' && $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') <= 0 ) 
            {
                $response = '{"responseEvent":"error","responseType":"' . $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] . '","serverResponse":"invalid gamble state"}';
                exit( $response );
            }
            if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'getSettings' ) 
            {
                $lastEvent = $slotSettings->GetHistory();
                if( $lastEvent != 'NULL' ) 
                {
                    if( isset($lastEvent->serverResponse->expSymbol) ) 
                    {
                        $slotSettings->SetGameData($slotSettings->slotId . 'ExpSymbol', $lastEvent->serverResponse->expSymbol);
                    }
                    if( isset($lastEvent->serverResponse->bonusWin) ) 
                    {
                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                    }
                    else
                    {
                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->totalWin);
                    }
                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                    $slotSettings->SetGameData($slotSettings->slotId . 'ScatterWin', $lastEvent->serverResponse->scattersWinTmp);
                }
                $slotSettings->slotJackpot0 = $slotSettings->slotJackpot[0];
                $slotSettings->slotJackpot = $slotSettings->slotJackpot[1];
                $_obf_0D2307370C401A042D310F070D313207280830055B2A32 = json_encode($slotSettings);
                $lang = json_encode(\Lang::get('games.' . $game));
                $response = '{"responseEvent":"getSettings","slotLanguage":' . $lang . ',"serverResponse":' . $_obf_0D2307370C401A042D310F070D313207280830055B2A32 . '}';
            }
            else if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'gamble5GetUserCards' ) 
            {
                $Balance = $slotSettings->GetBalance();
                $_obf_0D2F3C2804395C38101A1C05232C1040362D390D1D3301 = rand(1, $slotSettings->GetGambleSettings());
                $_obf_0D040B162710402D331E2209370C14231F2C252B172601 = $slotSettings->GetGameData('AmazingStarsGTMDealerCard');
                $totalWin = $slotSettings->GetGameData('AmazingStarsGTMTotalWin');
                $_obf_0D2D1D15122B0303242922110A351334341A371C0C3101 = 0;
                $gambleChoice = $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['gambleChoice'] - 2;
                $_obf_0D1C0D102A3D1F22280124351A230D33190A15123B2522 = '';
                $_obf_0D150D131414123F011E1F09403221085B233717403C22 = [
                    2, 
                    3, 
                    4, 
                    5, 
                    6, 
                    7, 
                    8, 
                    9, 
                    10, 
                    11, 
                    12, 
                    13, 
                    14
                ];
                $_obf_0D0A0B32260E260B5C152A0C3C0717270A3B2C192C0211 = [
                    'C', 
                    'S', 
                    'D', 
                    'H'
                ];
                $_obf_0D5B182E0E11371B271E18193E040A1A28262E2A303611 = [
                    '', 
                    '', 
                    '2', 
                    '3', 
                    '4', 
                    '5', 
                    '6', 
                    '7', 
                    '8', 
                    '9', 
                    '10', 
                    'J', 
                    'Q', 
                    'K', 
                    'A'
                ];
                $_obf_0D1411191609280C1434232A2B0823093517151A283922 = 0;
                $_obf_0D31140A120E1B3D282A06280B27103B250935092B1F01 = $totalWin;
                if( $slotSettings->GetBank((isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ? $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] : '')) < ($totalWin * 2) ) 
                {
                    $_obf_0D2F3C2804395C38101A1C05232C1040362D390D1D3301 = 0;
                }
                if( $_obf_0D2F3C2804395C38101A1C05232C1040362D390D1D3301 == 1 ) 
                {
                    $_obf_0D1411191609280C1434232A2B0823093517151A283922 = rand($_obf_0D040B162710402D331E2209370C14231F2C252B172601, 14);
                }
                else
                {
                    $_obf_0D1411191609280C1434232A2B0823093517151A283922 = rand(2, $_obf_0D040B162710402D331E2209370C14231F2C252B172601);
                }
                if( $_obf_0D040B162710402D331E2209370C14231F2C252B172601 < $_obf_0D1411191609280C1434232A2B0823093517151A283922 ) 
                {
                    $_obf_0D2D1D15122B0303242922110A351334341A371C0C3101 = $totalWin;
                    $totalWin = $totalWin * 2;
                    $_obf_0D1C0D102A3D1F22280124351A230D33190A15123B2522 = 'win';
                }
                else if( $_obf_0D1411191609280C1434232A2B0823093517151A283922 < $_obf_0D040B162710402D331E2209370C14231F2C252B172601 ) 
                {
                    $_obf_0D2D1D15122B0303242922110A351334341A371C0C3101 = -1 * $totalWin;
                    $totalWin = 0;
                    $_obf_0D1C0D102A3D1F22280124351A230D33190A15123B2522 = 'lose';
                }
                else
                {
                    $_obf_0D2D1D15122B0303242922110A351334341A371C0C3101 = $totalWin;
                    $totalWin = $totalWin;
                    $_obf_0D1C0D102A3D1F22280124351A230D33190A15123B2522 = 'draw';
                }
                if( $_obf_0D2D1D15122B0303242922110A351334341A371C0C3101 != $totalWin ) 
                {
                    $slotSettings->SetBalance($_obf_0D2D1D15122B0303242922110A351334341A371C0C3101);
                    $slotSettings->SetBank((isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ? $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] : ''), $_obf_0D2D1D15122B0303242922110A351334341A371C0C3101 * -1);
                }
                $_obf_0D18161E352E061A0433191E381C2E0B222A272A2D3E22 = $slotSettings->GetBalance();
                $_obf_0D0F0A0C0B11232E3F18330D2F283003181C24311E0F32 = [
                    rand(2, 14), 
                    rand(2, 14), 
                    rand(2, 14), 
                    rand(2, 14)
                ];
                $_obf_0D0F0A0C0B11232E3F18330D2F283003181C24311E0F32[$gambleChoice] = $_obf_0D1411191609280C1434232A2B0823093517151A283922;
                for( $i = 0; $i < 4; $i++ ) 
                {
                    $_obf_0D0F0A0C0B11232E3F18330D2F283003181C24311E0F32[$i] = '"' . $_obf_0D5B182E0E11371B271E18193E040A1A28262E2A303611[$_obf_0D0F0A0C0B11232E3F18330D2F283003181C24311E0F32[$i]] . $_obf_0D0A0B32260E260B5C152A0C3C0717270A3B2C192C0211[rand(0, 3)] . '"';
                }
                $_obf_0D3504060F0735361A0C112A5C231C1E08031E3B0B3D32 = implode(',', $_obf_0D0F0A0C0B11232E3F18330D2F283003181C24311E0F32);
                $slotSettings->SetGameData('AmazingStarsGTMTotalWin', $totalWin);
                $_obf_0D2307370C401A042D310F070D313207280830055B2A32 = '{"dealerCard":"' . $_obf_0D040B162710402D331E2209370C14231F2C252B172601 . '","playerCards":[' . $_obf_0D3504060F0735361A0C112A5C231C1E08031E3B0B3D32 . '],"gambleState":"' . $_obf_0D1C0D102A3D1F22280124351A230D33190A15123B2522 . '","totalWin":' . $totalWin . ',"afterBalance":' . $_obf_0D18161E352E061A0433191E381C2E0B222A272A2D3E22 . ',"Balance":' . $Balance . '}';
                $response = '{"responseEvent":"gambleResult","deb":' . $_obf_0D0F0A0C0B11232E3F18330D2F283003181C24311E0F32[$gambleChoice] . ',"serverResponse":' . $_obf_0D2307370C401A042D310F070D313207280830055B2A32 . '}';
            }
            else if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'gamble5GetDealerCard' ) 
            {
                $_obf_0D150D131414123F011E1F09403221085B233717403C22 = [
                    2, 
                    3, 
                    4, 
                    5, 
                    6, 
                    7, 
                    8, 
                    9, 
                    10, 
                    11, 
                    12, 
                    13, 
                    14
                ];
                $_obf_0D5B182E0E11371B271E18193E040A1A28262E2A303611 = [
                    '', 
                    '', 
                    '2', 
                    '3', 
                    '4', 
                    '5', 
                    '6', 
                    '7', 
                    '8', 
                    '9', 
                    '10', 
                    'J', 
                    'Q', 
                    'K', 
                    'A'
                ];
                $_obf_0D0A0B32260E260B5C152A0C3C0717270A3B2C192C0211 = [
                    'C', 
                    'S', 
                    'D', 
                    'H'
                ];
                $_obf_0D5B5C0F232838114030190505402A241E131019332901 = $_obf_0D150D131414123F011E1F09403221085B233717403C22[rand(0, 12)];
                $slotSettings->SetGameData('AmazingStarsGTMDealerCard', $_obf_0D5B5C0F232838114030190505402A241E131019332901);
                $_obf_0D040B162710402D331E2209370C14231F2C252B172601 = $_obf_0D5B182E0E11371B271E18193E040A1A28262E2A303611[$_obf_0D5B5C0F232838114030190505402A241E131019332901] . $_obf_0D0A0B32260E260B5C152A0C3C0717270A3B2C192C0211[rand(0, 3)];
                $_obf_0D2307370C401A042D310F070D313207280830055B2A32 = '{"dealerCard":"' . $_obf_0D040B162710402D331E2209370C14231F2C252B172601 . '"}';
                $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $_obf_0D2307370C401A042D310F070D313207280830055B2A32 . '}';
            }
            else if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'slotGamble' ) 
            {
                $Balance = $slotSettings->GetBalance();
                $_obf_0D2F3C2804395C38101A1C05232C1040362D390D1D3301 = rand(1, $slotSettings->GetGambleSettings());
                $_obf_0D040B162710402D331E2209370C14231F2C252B172601 = '';
                $totalWin = $slotSettings->GetGameData('AmazingStarsGTMTotalWin');
                $_obf_0D2D1D15122B0303242922110A351334341A371C0C3101 = 0;
                $_obf_0D31140A120E1B3D282A06280B27103B250935092B1F01 = $totalWin;
                if( $slotSettings->GetBank((isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ? $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] : '')) < ($totalWin * 2) ) 
                {
                    $_obf_0D2F3C2804395C38101A1C05232C1040362D390D1D3301 = 0;
                }
                if( $_obf_0D2F3C2804395C38101A1C05232C1040362D390D1D3301 == 1 ) 
                {
                    $_obf_0D1C0D102A3D1F22280124351A230D33190A15123B2522 = 'win';
                    $_obf_0D2D1D15122B0303242922110A351334341A371C0C3101 = $totalWin;
                    $totalWin = $totalWin * 2;
                    if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['gambleChoice'] == 'red' ) 
                    {
                        $_obf_0D1D3229361F0A3E062B2224050A0E013D0F1911072E01 = [
                            'D', 
                            'H'
                        ];
                        $_obf_0D040B162710402D331E2209370C14231F2C252B172601 = $_obf_0D1D3229361F0A3E062B2224050A0E013D0F1911072E01[rand(0, 1)];
                    }
                    else
                    {
                        $_obf_0D1D3229361F0A3E062B2224050A0E013D0F1911072E01 = [
                            'C', 
                            'S'
                        ];
                        $_obf_0D040B162710402D331E2209370C14231F2C252B172601 = $_obf_0D1D3229361F0A3E062B2224050A0E013D0F1911072E01[rand(0, 1)];
                    }
                }
                else
                {
                    $_obf_0D1C0D102A3D1F22280124351A230D33190A15123B2522 = 'lose';
                    $_obf_0D2D1D15122B0303242922110A351334341A371C0C3101 = -1 * $totalWin;
                    $totalWin = 0;
                    if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['gambleChoice'] == 'red' ) 
                    {
                        $_obf_0D1D3229361F0A3E062B2224050A0E013D0F1911072E01 = [
                            'C', 
                            'S'
                        ];
                        $_obf_0D040B162710402D331E2209370C14231F2C252B172601 = $_obf_0D1D3229361F0A3E062B2224050A0E013D0F1911072E01[rand(0, 1)];
                    }
                    else
                    {
                        $_obf_0D1D3229361F0A3E062B2224050A0E013D0F1911072E01 = [
                            'D', 
                            'H'
                        ];
                        $_obf_0D040B162710402D331E2209370C14231F2C252B172601 = $_obf_0D1D3229361F0A3E062B2224050A0E013D0F1911072E01[rand(0, 1)];
                    }
                }
                $slotSettings->SetGameData('AmazingStarsGTMTotalWin', $totalWin);
                $slotSettings->SetBalance($_obf_0D2D1D15122B0303242922110A351334341A371C0C3101);
                $slotSettings->SetBank((isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ? $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] : ''), $_obf_0D2D1D15122B0303242922110A351334341A371C0C3101 * -1);
                $_obf_0D18161E352E061A0433191E381C2E0B222A272A2D3E22 = $slotSettings->GetBalance();
                $_obf_0D2307370C401A042D310F070D313207280830055B2A32 = '{"dealerCard":"' . $_obf_0D040B162710402D331E2209370C14231F2C252B172601 . '","gambleState":"' . $_obf_0D1C0D102A3D1F22280124351A230D33190A15123B2522 . '","totalWin":' . $totalWin . ',"afterBalance":' . $_obf_0D18161E352E061A0433191E381C2E0B222A272A2D3E22 . ',"Balance":' . $Balance . '}';
                $response = '{"responseEvent":"gambleResult","serverResponse":' . $_obf_0D2307370C401A042D310F070D313207280830055B2A32 . '}';
                $slotSettings->SaveLogReport($response, $_obf_0D31140A120E1B3D282A06280B27103B250935092B1F01, 1, $_obf_0D2D1D15122B0303242922110A351334341A371C0C3101, $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']);
            }
            else if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'bet' || $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' ) 
            {
                $linesId = [];
                $linesId[0] = [
                    2, 
                    2, 
                    2, 
                    2, 
                    2
                ];
                $linesId[1] = [
                    1, 
                    1, 
                    1, 
                    1, 
                    1
                ];
                $linesId[2] = [
                    3, 
                    3, 
                    3, 
                    3, 
                    3
                ];
                $linesId[3] = [
                    1, 
                    2, 
                    3, 
                    2, 
                    1
                ];
                $linesId[4] = [
                    3, 
                    2, 
                    1, 
                    2, 
                    3
                ];
                $linesId[5] = [
                    2, 
                    3, 
                    3, 
                    3, 
                    2
                ];
                $linesId[6] = [
                    2, 
                    1, 
                    1, 
                    1, 
                    2
                ];
                $linesId[7] = [
                    3, 
                    3, 
                    2, 
                    1, 
                    1
                ];
                $linesId[8] = [
                    1, 
                    1, 
                    2, 
                    3, 
                    3
                ];
                $linesId[9] = [
                    3, 
                    2, 
                    2, 
                    2, 
                    1
                ];
                $_obf_0D360F0140113330275B14311E3516150112390A0F1B22 = $slotSettings->GetSpinSettings($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'], $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'], $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines']);
                $winType = $_obf_0D360F0140113330275B14311E3516150112390A0F1B22[0];
                $_obf_0D3030072F273706293C133F2F072B113B383322291201 = $_obf_0D360F0140113330275B14311E3516150112390A0F1B22[1];
                if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] != 'freespin' ) 
                {
                    if( !isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ) 
                    {
                        $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] = 'bet';
                    }
                    $_obf_0D2A0526273612293511363C26193E1C130B2719192611 = ($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines']) / 100 * $slotSettings->GetPercent();
                    $slotSettings->SetBank((isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ? $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] : ''), $_obf_0D2A0526273612293511363C26193E1C130B2719192611, $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']);
                    $_obf_0D3F08152E011C2E053E0C34233B231F3D212F3C1B2A22 = $slotSettings->UpdateJackpots($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'], false);
                    $slotSettings->SetBalance(-1 * ($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines']), $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']);
                    $bonusMpl = 1;
                    $slotSettings->SetGameData('AmazingStarsGTMBonusWin', 0);
                    $slotSettings->SetGameData('AmazingStarsGTMFreeGames', 0);
                    $slotSettings->SetGameData('AmazingStarsGTMCurrentFreeGame', 0);
                    $slotSettings->SetGameData('AmazingStarsGTMTotalWin', 0);
                    $slotSettings->SetGameData('AmazingStarsGTMFreeBalance', 0);
                    $slotSettings->SetGameData('AmazingStarsGTMScatterWin', 0);
                    $slotSettings->SetGameData('AmazingStarsGTMFreeStacked', [
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false', 
                        'false'
                    ]);
                    for( $ii = 0; $ii < 16; $ii++ ) 
                    {
                        $slotSettings->SetGameData('AmazingStarsGTMFreeStacked.' . $ii, 'false');
                    }
                }
                else
                {
                    $slotSettings->SetGameData('AmazingStarsGTMCurrentFreeGame', $slotSettings->GetGameData('AmazingStarsGTMCurrentFreeGame') + 1);
                    $bonusMpl = $slotSettings->slotFreeMpl;
                    if( $slotSettings->GetGameData('AmazingStarsGTMFreeGames') == $slotSettings->GetGameData('AmazingStarsGTMCurrentFreeGame') ) 
                    {
                        $_obf_0D3F08152E011C2E053E0C34233B231F3D212F3C1B2A22 = $slotSettings->UpdateJackpots($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'], true);
                    }
                }
                $Balance = $slotSettings->GetBalance();
                for( $i = 0; $i <= 2000; $i++ ) 
                {
                    $_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 = false;
                    $totalWin = 0;
                    $lineWins = [];
                    $cWins = [
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0
                    ];
                    $_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 = false;
                    $wild = ['NONE'];
                    $scatter = 'SCAT';
                    $reels = $slotSettings->GetReelStrips($winType, $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']);
                    if( isset($_obf_0D3F08152E011C2E053E0C34233B231F3D212F3C1B2A22) && $_obf_0D3F08152E011C2E053E0C34233B231F3D212F3C1B2A22['isJackPay'] ) 
                    {
                        $_obf_0D07151D39191237311B250631072E2502142E0B2A0B22 = 1;
                        for( $_obf_0D0E361C162906193F350C2711233B34010C2F0E232632 = 1; $_obf_0D0E361C162906193F350C2711233B34010C2F0E232632 <= 5; $_obf_0D0E361C162906193F350C2711233B34010C2F0E232632++ ) 
                        {
                            if( $_obf_0D3F08152E011C2E053E0C34233B231F3D212F3C1B2A22['isJackId'] == 0 ) 
                            {
                                $_obf_0D0D21221110082E2F1140152E3C1C370E100601132101 = $slotSettings->PutBonusToLine($_obf_0D0E361C162906193F350C2711233B34010C2F0E232632, $linesId[$_obf_0D07151D39191237311B250631072E2502142E0B2A0B22][$_obf_0D0E361C162906193F350C2711233B34010C2F0E232632 - 1], 'SCAT');
                                $reels['reel' . $_obf_0D0E361C162906193F350C2711233B34010C2F0E232632] = [
                                    'SCAT', 
                                    'SCAT', 
                                    'SCAT', 
                                    ''
                                ];
                                $reels['rp'][$_obf_0D0E361C162906193F350C2711233B34010C2F0E232632 - 1] = $_obf_0D0D21221110082E2F1140152E3C1C370E100601132101['rp'];
                                $winType = 'none';
                            }
                            else
                            {
                                $_obf_0D0D21221110082E2F1140152E3C1C370E100601132101 = $slotSettings->PutBonusToLine($_obf_0D0E361C162906193F350C2711233B34010C2F0E232632, $linesId[$_obf_0D07151D39191237311B250631072E2502142E0B2A0B22][$_obf_0D0E361C162906193F350C2711233B34010C2F0E232632 - 1], 'P_1');
                                $reels['reel' . $_obf_0D0E361C162906193F350C2711233B34010C2F0E232632] = $_obf_0D0D21221110082E2F1140152E3C1C370E100601132101['reel'];
                                $reels['rp'][$_obf_0D0E361C162906193F350C2711233B34010C2F0E232632 - 1] = $_obf_0D0D21221110082E2F1140152E3C1C370E100601132101['rp'];
                                $winType = 'none';
                            }
                        }
                    }
                    $_obf_0D2613221F3511102909331A5C2E1B1D085C3B05120332 = $reels;
                    if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' ) 
                    {
                        $_obf_0D2F03271A182908250A12123018172F403C18330A2B22 = 1;
                        $_obf_0D152805223C282F383F1C0E1A260D343C032417101311 = $slotSettings->GetGameData('AmazingStarsGTMFreeStacked');
                        for( $ii = 1; $ii <= 5; $ii++ ) 
                        {
                            if( $_obf_0D152805223C282F383F1C0E1A260D343C032417101311[$_obf_0D2F03271A182908250A12123018172F403C18330A2B22] == 'true' ) 
                            {
                                $reels['reel' . $ii][0] = 'SCAT';
                            }
                            $_obf_0D2F03271A182908250A12123018172F403C18330A2B22++;
                            if( $_obf_0D152805223C282F383F1C0E1A260D343C032417101311[$_obf_0D2F03271A182908250A12123018172F403C18330A2B22] == 'true' ) 
                            {
                                $reels['reel' . $ii][1] = 'SCAT';
                            }
                            $_obf_0D2F03271A182908250A12123018172F403C18330A2B22++;
                            if( $_obf_0D152805223C282F383F1C0E1A260D343C032417101311[$_obf_0D2F03271A182908250A12123018172F403C18330A2B22] == 'true' ) 
                            {
                                $reels['reel' . $ii][2] = 'SCAT';
                            }
                            $_obf_0D2F03271A182908250A12123018172F403C18330A2B22++;
                        }
                    }
                    for( $k = 0; $k < $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines']; $k++ ) 
                    {
                        $_obf_0D02100911023C3C260E0C262F5B2C1D2839310E112A32 = '';
                        for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                        {
                            $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 = $slotSettings->SymbolGame[$j];
                            if( $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 == $scatter || !isset($slotSettings->Paytable[$_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11]) || $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' ) 
                            {
                            }
                            else
                            {
                                $s = [];
                                $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                if( $s[0] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[0], $wild) ) 
                                {
                                    $mpl = 1;
                                    $_obf_0D365B172533170712222423300A1B092C161521071B32 = $slotSettings->Paytable[$_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11][1] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $mpl * $bonusMpl;
                                    if( $cWins[$k] < $_obf_0D365B172533170712222423300A1B092C161521071B32 ) 
                                    {
                                        $cWins[$k] = $_obf_0D365B172533170712222423300A1B092C161521071B32;
                                        $_obf_0D02100911023C3C260E0C262F5B2C1D2839310E112A32 = '{"Count":1,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                    }
                                }
                                if( ($s[0] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[0], $wild)) && ($s[1] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[1], $wild)) ) 
                                {
                                    $mpl = 1;
                                    if( in_array($s[0], $wild) && in_array($s[1], $wild) ) 
                                    {
                                        $mpl = 1;
                                    }
                                    else if( in_array($s[0], $wild) || in_array($s[1], $wild) ) 
                                    {
                                        $mpl = $slotSettings->slotWildMpl;
                                        for( $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32 = 0; $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32 < 2; $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32++ ) 
                                        {
                                            if( in_array($s[$_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32], $wild) ) 
                                            {
                                                $s[$_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32] = 'P_1';
                                            }
                                        }
                                    }
                                    $_obf_0D365B172533170712222423300A1B092C161521071B32 = $slotSettings->Paytable[$_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11][2] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $mpl * $bonusMpl;
                                    if( $cWins[$k] < $_obf_0D365B172533170712222423300A1B092C161521071B32 ) 
                                    {
                                        $cWins[$k] = $_obf_0D365B172533170712222423300A1B092C161521071B32;
                                        $_obf_0D02100911023C3C260E0C262F5B2C1D2839310E112A32 = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                    }
                                }
                                $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                if( ($s[0] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[0], $wild)) && ($s[1] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[1], $wild)) && ($s[2] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[2], $wild)) ) 
                                {
                                    $mpl = 1;
                                    if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) ) 
                                    {
                                        $mpl = 1;
                                    }
                                    else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) ) 
                                    {
                                        $mpl = $slotSettings->slotWildMpl;
                                        for( $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32 = 0; $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32 < 3; $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32++ ) 
                                        {
                                            if( in_array($s[$_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32], $wild) ) 
                                            {
                                                $s[$_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32] = 'P_1';
                                            }
                                        }
                                    }
                                    $_obf_0D365B172533170712222423300A1B092C161521071B32 = $slotSettings->Paytable[$_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11][3] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $mpl * $bonusMpl;
                                    if( $cWins[$k] < $_obf_0D365B172533170712222423300A1B092C161521071B32 ) 
                                    {
                                        $cWins[$k] = $_obf_0D365B172533170712222423300A1B092C161521071B32;
                                        $_obf_0D02100911023C3C260E0C262F5B2C1D2839310E112A32 = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                    }
                                }
                                $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                if( ($s[0] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[0], $wild)) && ($s[1] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[1], $wild)) && ($s[2] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[2], $wild)) && ($s[3] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[3], $wild)) ) 
                                {
                                    $mpl = 1;
                                    if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) ) 
                                    {
                                        $mpl = 1;
                                    }
                                    else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) ) 
                                    {
                                        $mpl = $slotSettings->slotWildMpl;
                                        for( $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32 = 0; $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32 < 4; $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32++ ) 
                                        {
                                            if( in_array($s[$_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32], $wild) ) 
                                            {
                                                $s[$_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32] = 'P_1';
                                            }
                                        }
                                    }
                                    $_obf_0D365B172533170712222423300A1B092C161521071B32 = $slotSettings->Paytable[$_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11][4] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $mpl * $bonusMpl;
                                    if( $cWins[$k] < $_obf_0D365B172533170712222423300A1B092C161521071B32 ) 
                                    {
                                        $cWins[$k] = $_obf_0D365B172533170712222423300A1B092C161521071B32;
                                        $_obf_0D02100911023C3C260E0C262F5B2C1D2839310E112A32 = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
                                    }
                                }
                                $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                if( ($s[0] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[0], $wild)) && ($s[1] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[1], $wild)) && ($s[2] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[2], $wild)) && ($s[3] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[3], $wild)) && ($s[4] == $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 || in_array($s[4], $wild)) ) 
                                {
                                    $mpl = 1;
                                    if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) && in_array($s[4], $wild) ) 
                                    {
                                        $mpl = 1;
                                    }
                                    else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) || in_array($s[4], $wild) ) 
                                    {
                                        $mpl = $slotSettings->slotWildMpl;
                                        for( $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32 = 0; $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32 < 5; $_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32++ ) 
                                        {
                                            if( in_array($s[$_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32], $wild) ) 
                                            {
                                                $s[$_obf_0D340A0912163F2A0525183C1C2C08141C231F5B2A5C32] = 'P_1';
                                            }
                                        }
                                    }
                                    $_obf_0D365B172533170712222423300A1B092C161521071B32 = $slotSettings->Paytable[$_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11][5] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $mpl * $bonusMpl;
                                    if( $_obf_0D350901162A195B273D0F282B290A2A0B1811330C0E11 == 'P_1' ) 
                                    {
                                        $_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 = true;
                                    }
                                    if( $_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 && !isset($slotSettings->Jackpots['jackPay']) ) 
                                    {
                                        $_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 = false;
                                    }
                                    else
                                    {
                                        if( $cWins[$k] < $_obf_0D365B172533170712222423300A1B092C161521071B32 && !$_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 ) 
                                        {
                                            $cWins[$k] = $_obf_0D365B172533170712222423300A1B092C161521071B32;
                                            $_obf_0D02100911023C3C260E0C262F5B2C1D2839310E112A32 = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
                                        }
                                        if( $_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 ) 
                                        {
                                            $cWins[$k] = $_obf_0D365B172533170712222423300A1B092C161521071B32;
                                            $_obf_0D02100911023C3C260E0C262F5B2C1D2839310E112A32 = '{"Count":5,"Line":' . $k . ',"Win":"' . $slotSettings->Jackpots['jackPay'] . '","stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
                                        }
                                    }
                                }
                            }
                        }
                        if( $cWins[$k] > 0 && $_obf_0D02100911023C3C260E0C262F5B2C1D2839310E112A32 != '' || $_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 && $_obf_0D02100911023C3C260E0C262F5B2C1D2839310E112A32 != '' ) 
                        {
                            array_push($lineWins, $_obf_0D02100911023C3C260E0C262F5B2C1D2839310E112A32);
                            $totalWin += $cWins[$k];
                        }
                    }
                    $scattersWin = 0;
                    $scattersStr = '{';
                    $_obf_0D022B3C29282B081411243D2F01123F241F0C1B040C32 = '{';
                    $scattersCount = 0;
                    $_obf_0D370C042736073E3408282E1318280C171E2209352732 = false;
                    $_obf_0D1603170E181E05093E17132423360A391A210B285B11 = 0;
                    $_obf_0D0B010C233B17122F1213180A34033B08263B1C360911 = 0;
                    for( $r = 1; $r <= 5; $r++ ) 
                    {
                        for( $_obf_0D31403C0837332A1A1711312A3415151610280F122122 = 0; $_obf_0D31403C0837332A1A1711312A3415151610280F122122 <= 2; $_obf_0D31403C0837332A1A1711312A3415151610280F122122++ ) 
                        {
                            if( $reels['reel' . $r][$_obf_0D31403C0837332A1A1711312A3415151610280F122122] == $scatter ) 
                            {
                                $scattersCount++;
                                $scattersStr .= ('"winReel' . $r . '_' . $_obf_0D31403C0837332A1A1711312A3415151610280F122122 . '":[' . $_obf_0D31403C0837332A1A1711312A3415151610280F122122 . ',"' . $scatter . '"],');
                                $_obf_0D022B3C29282B081411243D2F01123F241F0C1B040C32 .= ('"winReel' . $r . '_' . $_obf_0D31403C0837332A1A1711312A3415151610280F122122 . '":[' . $_obf_0D31403C0837332A1A1711312A3415151610280F122122 . ',"' . $scatter . '"],');
                            }
                            if( $reels['reel' . $r][$_obf_0D31403C0837332A1A1711312A3415151610280F122122] == 'P_1' ) 
                            {
                                $_obf_0D1603170E181E05093E17132423360A391A210B285B11++;
                            }
                            if( $reels['reel' . $r][$_obf_0D31403C0837332A1A1711312A3415151610280F122122] == 'SCAT' ) 
                            {
                                $_obf_0D0B010C233B17122F1213180A34033B08263B1C360911++;
                            }
                        }
                    }
                    if( $_obf_0D0B010C233B17122F1213180A34033B08263B1C360911 >= 15 ) 
                    {
                        $totalWin = 0;
                        $_obf_0D370C042736073E3408282E1318280C171E2209352732 = true;
                    }
                    $scattersWinTmp = 0;
                    if( $slotSettings->GetGameData('AmazingStarsGTMFreeGames') <= $slotSettings->GetGameData('AmazingStarsGTMCurrentFreeGame') && $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' ) 
                    {
                        $scattersWinTmp = $slotSettings->Paytable[$scatter][$scattersCount] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'] - $slotSettings->GetGameData('AmazingStarsGTMScatterWin');
                        $_obf_0D195C0F2915230B5C17342A08251204342D3C1F024001 = $slotSettings->GetBank((isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ? $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] : ''));
                        $_obf_0D3030072F273706293C133F2F072B113B383322291201 = $_obf_0D195C0F2915230B5C17342A08251204342D3C1F024001;
                    }
                    else if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' ) 
                    {
                        $scattersWinTmp = $slotSettings->Paytable[$scatter][$scattersCount] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'] - $slotSettings->GetGameData('AmazingStarsGTMScatterWin');
                    }
                    else if( $winType == 'bonus' ) 
                    {
                        $scattersWinTmp = $slotSettings->Paytable[$scatter][$scattersCount] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'] - $slotSettings->GetGameData('AmazingStarsGTMScatterWin');
                    }
                    else
                    {
                        $scattersWin = 0;
                    }
                    if( $scattersCount >= 3 && $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] != 'freespin' ) 
                    {
                        $scattersStr .= '"scattersType":"bonus",';
                        $scattersWin = 0;
                    }
                    else if( $scattersWin > 0 ) 
                    {
                        $scattersStr .= '"scattersType":"win",';
                    }
                    else
                    {
                        $scattersStr .= '"scattersType":"none",';
                    }
                    $scattersStr .= ('"scattersWin":' . $scattersWin . '}');
                    $totalWin += $scattersWin;
                    if( $_obf_0D370C042736073E3408282E1318280C171E2209352732 && $_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 ) 
                    {
                        $totalWin = 0;
                        $winType = 'none';
                    }
                    if( $_obf_0D370C042736073E3408282E1318280C171E2209352732 ) 
                    {
                        $totalWin = 0;
                        break;
                    }
                    if( $_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 ) 
                    {
                        $totalWin = $slotSettings->Jackpots['jackPay'];
                        break;
                    }
                    if( $i > 1000 ) 
                    {
                        $winType = 'none';
                    }
                    if( $i > 1500 ) 
                    {
                        $response = '{"responseEvent":"error","responseType":"' . $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                        exit( $response );
                    }
                    if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] != 'freespin' ) 
                    {
                        if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] * rand(2, 5)) ) 
                        {
                        }
                        else if( !$slotSettings->increaseRTP && $winType == 'win' && $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'] * $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] < $totalWin ) 
                        {
                        }
                    }
                    if( $_obf_0D0B010C233B17122F1213180A34033B08263B1C360911 >= 15 && !$_obf_0D3F08152E011C2E053E0C34233B231F3D212F3C1B2A22['isJackPay'] ) 
                    {
                    }
                    else if( $slotSettings->GetBank((isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ? $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] : '')) < ($totalWin + $scattersWinTmp) && ($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' || $winType == 'bonus') ) 
                    {
                    }
                    else
                    {
                        if( $slotSettings->GetGameData('AmazingStarsGTMFreeGames') <= $slotSettings->GetGameData('AmazingStarsGTMCurrentFreeGame') && $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' ) 
                        {
                            $scattersWin = 0;
                            $totalWin = 0;
                            $_obf_0D195C0F2915230B5C17342A08251204342D3C1F024001 = $slotSettings->GetBank((isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ? $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] : ''));
                            $_obf_0D3030072F273706293C133F2F072B113B383322291201 = $_obf_0D195C0F2915230B5C17342A08251204342D3C1F024001;
                            if( $totalWin <= $_obf_0D3030072F273706293C133F2F072B113B383322291201 ) 
                            {
                                break;
                            }
                        }
                        if( $scattersCount >= 3 && $winType != 'bonus' && $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] != 'freespin' ) 
                        {
                        }
                        else if( $totalWin <= $_obf_0D3030072F273706293C133F2F072B113B383322291201 && $winType == 'bonus' ) 
                        {
                            $_obf_0D195C0F2915230B5C17342A08251204342D3C1F024001 = $slotSettings->GetBank((isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ? $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] : ''));
                            if( $_obf_0D195C0F2915230B5C17342A08251204342D3C1F024001 < $_obf_0D3030072F273706293C133F2F072B113B383322291201 ) 
                            {
                                $_obf_0D3030072F273706293C133F2F072B113B383322291201 = $_obf_0D195C0F2915230B5C17342A08251204342D3C1F024001;
                            }
                            else
                            {
                                break;
                            }
                        }
                        else if( $totalWin > 0 && $totalWin <= $_obf_0D3030072F273706293C133F2F072B113B383322291201 && $winType == 'win' ) 
                        {
                            $_obf_0D195C0F2915230B5C17342A08251204342D3C1F024001 = $slotSettings->GetBank((isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ? $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] : ''));
                            if( $_obf_0D195C0F2915230B5C17342A08251204342D3C1F024001 < $_obf_0D3030072F273706293C133F2F072B113B383322291201 ) 
                            {
                                $_obf_0D3030072F273706293C133F2F072B113B383322291201 = $_obf_0D195C0F2915230B5C17342A08251204342D3C1F024001;
                            }
                            else
                            {
                                break;
                            }
                        }
                        else if( $totalWin == 0 && $winType == 'none' ) 
                        {
                            break;
                        }
                    }
                }
                if( $totalWin + $scattersWinTmp > 0 && !$_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 && !$_obf_0D370C042736073E3408282E1318280C171E2209352732 ) 
                {
                    $slotSettings->SetBank((isset($_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']) ? $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] : ''), -1 * ($totalWin + $scattersWinTmp));
                    if( $scattersWinTmp > 0 ) 
                    {
                        $slotSettings->SetGameData('AmazingStarsGTMScatterWin', $slotSettings->GetGameData('AmazingStarsGTMScatterWin') + $scattersWinTmp);
                    }
                }
                if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' && $slotSettings->GetGameData('AmazingStarsGTMFreeGames') <= $slotSettings->GetGameData('AmazingStarsGTMCurrentFreeGame') && $winType != 'bonus' && $slotSettings->GetGameData('AmazingStarsGTMTotalWin') + $totalWin + $slotSettings->GetGameData('AmazingStarsGTMScatterWin') > 0 && !$_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 && !$_obf_0D370C042736073E3408282E1318280C171E2209352732 ) 
                {
                    $slotSettings->SetBalance($slotSettings->GetGameData('AmazingStarsGTMTotalWin') + $totalWin + $slotSettings->GetGameData('AmazingStarsGTMScatterWin'));
                }
                else if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] != 'freespin' && $winType != 'bonus' && $totalWin > 0 && !$_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 && !$_obf_0D370C042736073E3408282E1318280C171E2209352732 ) 
                {
                    $slotSettings->SetBalance($totalWin);
                }
                $_obf_0D0C361D2E35362209025C2317232809271D34270D3232 = $totalWin;
                if( $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' ) 
                {
                    $slotSettings->SetGameData('AmazingStarsGTMBonusWin', $slotSettings->GetGameData('AmazingStarsGTMBonusWin') + $totalWin);
                    $slotSettings->SetGameData('AmazingStarsGTMTotalWin', $slotSettings->GetGameData('AmazingStarsGTMTotalWin') + $totalWin);
                    $totalWin = $slotSettings->GetGameData('AmazingStarsGTMBonusWin');
                    $Balance = $slotSettings->GetGameData('AmazingStarsGTMFreeBalance');
                    $_obf_0D2F03271A182908250A12123018172F403C18330A2B22 = 1;
                    $_obf_0D16193F0E253C0237030110222608163D2C221D1A0411 = $slotSettings->GetGameData('AmazingStarsGTMFreeStacked');
                    for( $i = 1; $i <= 5; $i++ ) 
                    {
                        if( $reels['reel' . $i][0] == 'SCAT' ) 
                        {
                            $_obf_0D16193F0E253C0237030110222608163D2C221D1A0411[$_obf_0D2F03271A182908250A12123018172F403C18330A2B22] = 'true';
                        }
                        $_obf_0D2F03271A182908250A12123018172F403C18330A2B22++;
                        if( $reels['reel' . $i][1] == 'SCAT' ) 
                        {
                            $_obf_0D16193F0E253C0237030110222608163D2C221D1A0411[$_obf_0D2F03271A182908250A12123018172F403C18330A2B22] = 'true';
                        }
                        $_obf_0D2F03271A182908250A12123018172F403C18330A2B22++;
                        if( $reels['reel' . $i][2] == 'SCAT' ) 
                        {
                            $_obf_0D16193F0E253C0237030110222608163D2C221D1A0411[$_obf_0D2F03271A182908250A12123018172F403C18330A2B22] = 'true';
                        }
                        $_obf_0D2F03271A182908250A12123018172F403C18330A2B22++;
                    }
                    $slotSettings->SetGameData('AmazingStarsGTMFreeStacked', $_obf_0D16193F0E253C0237030110222608163D2C221D1A0411);
                }
                else
                {
                    $slotSettings->SetGameData('AmazingStarsGTMTotalWin', $totalWin);
                }
                if( $scattersCount >= 3 && $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] != 'freespin' ) 
                {
                    if( $slotSettings->GetGameData('AmazingStarsGTMFreeGames') > 0 ) 
                    {
                        $slotSettings->SetGameData('AmazingStarsGTMFreeBalance', $Balance);
                        $slotSettings->SetGameData('AmazingStarsGTMBonusWin', $totalWin);
                        $slotSettings->SetGameData('AmazingStarsGTMFreeGames', $slotSettings->GetGameData('AmazingStarsGTMFreeGames') + $slotSettings->slotFreeCount);
                    }
                    else
                    {
                        $slotSettings->SetGameData('AmazingStarsGTMFreeBalance', $Balance);
                        $slotSettings->SetGameData('AmazingStarsGTMBonusWin', $totalWin);
                        $slotSettings->SetGameData('AmazingStarsGTMFreeGames', $slotSettings->slotFreeCount);
                        $_obf_0D2F03271A182908250A12123018172F403C18330A2B22 = 1;
                        $_obf_0D16193F0E253C0237030110222608163D2C221D1A0411 = $slotSettings->GetGameData('AmazingStarsGTMFreeStacked');
                        for( $i = 1; $i <= 5; $i++ ) 
                        {
                            if( $reels['reel' . $i][0] == 'SCAT' ) 
                            {
                                $_obf_0D16193F0E253C0237030110222608163D2C221D1A0411[$_obf_0D2F03271A182908250A12123018172F403C18330A2B22] = 'true';
                            }
                            $_obf_0D2F03271A182908250A12123018172F403C18330A2B22++;
                            if( $reels['reel' . $i][1] == 'SCAT' ) 
                            {
                                $_obf_0D16193F0E253C0237030110222608163D2C221D1A0411[$_obf_0D2F03271A182908250A12123018172F403C18330A2B22] = 'true';
                            }
                            $_obf_0D2F03271A182908250A12123018172F403C18330A2B22++;
                            if( $reels['reel' . $i][2] == 'SCAT' ) 
                            {
                                $_obf_0D16193F0E253C0237030110222608163D2C221D1A0411[$_obf_0D2F03271A182908250A12123018172F403C18330A2B22] = 'true';
                            }
                            $_obf_0D2F03271A182908250A12123018172F403C18330A2B22++;
                        }
                        $slotSettings->SetGameData('AmazingStarsGTMFreeStacked', $_obf_0D16193F0E253C0237030110222608163D2C221D1A0411);
                    }
                }
                $reels = $_obf_0D2613221F3511102909331A5C2E1B1D085C3B05120332;
                $_obf_0D273522403840350F0A36072E150A0524143F382C3B32 = '' . json_encode($reels) . '';
                $_obf_0D28393910101E062539311B3F371C121912162B061E32 = '' . json_encode($slotSettings->Jackpots) . '';
                $_obf_0D5B5C2E0D1C3D1F232F3E051D3225380127293C2A2432 = implode(',', $lineWins);
                $AmazingStarsGTMFreeStacked = [
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0', 
                    '0'
                ];
                $_obf_0D211C0D1F091E3F354003392130153E053437305B5C22 = $slotSettings->GetGameData('AmazingStarsGTMFreeStacked');
                for( $i = 0; $i <= 15; $i++ ) 
                {
                    if( $_obf_0D211C0D1F091E3F354003392130153E053437305B5C22[$i] == 'true' ) 
                    {
                        $AmazingStarsGTMFreeStacked[$i] = '1';
                    }
                }
                if( $slotSettings->GetGameData('AmazingStarsGTMFreeGames') <= $slotSettings->GetGameData('AmazingStarsGTMCurrentFreeGame') && $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] == 'freespin' && !$_obf_0D1C372E3C1F051E3D041C3909270E055B132904012232 && !$_obf_0D370C042736073E3408282E1318280C171E2209352732 ) 
                {
                    $totalWin += $slotSettings->GetGameData('AmazingStarsGTMScatterWin');
                    $_obf_0D0C361D2E35362209025C2317232809271D34270D3232 = $totalWin;
                    $_obf_0D022B3C29282B081411243D2F01123F241F0C1B040C32 .= '"scattersType":"win",';
                    $_obf_0D022B3C29282B081411243D2F01123F241F0C1B040C32 .= ('"scattersWin":' . $slotSettings->GetGameData('AmazingStarsGTMScatterWin') . '}');
                    $scattersStr = $_obf_0D022B3C29282B081411243D2F01123F241F0C1B040C32;
                }
                if( !isset($_obf_0D3F08152E011C2E053E0C34233B231F3D212F3C1B2A22) ) 
                {
                    $_obf_0D3F08152E011C2E053E0C34233B231F3D212F3C1B2A22 = [];
                }
                $response = '{"responseEvent":"spin","responseType":"' . $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent'] . '","serverResponse":{"$aws":[' . implode(',', $slotSettings->GetGameData('AmazingStarsGTMFreeStacked')) . '],"$jackState":' . json_encode($_obf_0D3F08152E011C2E053E0C34233B231F3D212F3C1B2A22) . ',"scattersWinTmp":' . $slotSettings->GetGameData('AmazingStarsGTMScatterWin') . ',"slotLines":' . $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'] . ',"slotBet":' . $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'] . ',"stackedWilds":[' . implode(',', $AmazingStarsGTMFreeStacked) . '],"slotJackpot0":' . $slotSettings->slotJackpot[0] . ',"slotJackpot":' . $slotSettings->slotJackpot[1] . ',"totalFreeGames":' . $slotSettings->GetGameData('AmazingStarsGTMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('AmazingStarsGTMCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"totalWin":' . $totalWin . ',"winLines":[' . $_obf_0D5B5C2E0D1C3D1F232F3E051D3225380127293C2A2432 . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $_obf_0D28393910101E062539311B3F371C121912162B061E32 . ',"reelsSymbols":' . $_obf_0D273522403840350F0A36072E150A0524143F382C3B32 . '}}';
                $slotSettings->SaveLogReport($response, $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'], $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'], $_obf_0D0C361D2E35362209025C2317232809271D34270D3232, $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotEvent']);
                if( isset($slotSettings->Jackpots['jackPay']) ) 
                {
                    $slotSettings->SaveLogReport($response, $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotBet'], $_obf_0D1027172F0A071202030538280B3D0B12241B16110E22['slotLines'], $slotSettings->Jackpots['jackPay'], 'JPG');
                }
            }
            $slotSettings->SaveGameData();
            \DB::commit();
            return $response;
        }
    }

}
