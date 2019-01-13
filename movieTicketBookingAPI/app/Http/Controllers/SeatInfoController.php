<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use App\SeatInfo;
use Illuminate\Http\Request;

class SeatInfoController extends Controller
{
    public function showAllSeatInfo()
    {
        return response()->json(SeatInfo::get());
    }

    public function showOneSeatInfo($seat_info_id)
    {
        return response()->json(SeatInfo::find($seat_info_id));
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'seatInfo' => 'required',
        ]);
        $seatInfo = $request['seatInfo'];
        foreach($seatInfo as $key=>$info){
            $row = $key;
            $numberOfSeats = $info['numberOfSeats'];
            $aisleSeats = implode(",", $info['aisleSeats']);
            SeatInfo::create(['screen' => $request['name'], 'row' => $row, 'noOfSeats' => $numberOfSeats, 'aisleSeats' => $aisleSeats]);
        }
        return response()->json('Success', 200);
    }

    public function getReq($screen_name){
        if(isset($_GET['status'])){
            if($_GET['status'] == 'unreserved'){
                return $this->getUnreserved($screen_name);
            }
        }
        else if(isset($_GET['numSeats']) && isset($_GET['choice'])){
            return $this->availability($screen_name, $_GET['numSeats'], $_GET['choice']);
        }
        return response()->json('Error: Invalid request', 201);
    }

    public function getUnreserved($screen_name){
        $res = DB::table('seat_info')->where('screen','=',$screen_name)->get();
        $response = array();
        foreach($res as $row){
            $reservedSeats = $row->reservedSeats;
            $noOfSeats = $row->noOfSeats;
            $aisleSeats = $row->aisleSeats;
            $aisleSeats = explode(",", $aisleSeats);
            $reservedSeats = explode(",", $reservedSeats);
            $unreservedSeats = array();
            for($i = 1; $i<=$noOfSeats; $i++){
                if(!in_array($i, $reservedSeats)){
                    array_push($unreservedSeats, $i);
                }
            } 
            $response['seats'][$row->row] = $unreservedSeats;
        }
        return response()->json($response, 200);
    }

    public function availability($screen_name, $x, $choice){
        $rowName = substr($choice, 0, 1);
        $seatNo = substr($choice, 1, 2);
        $row = DB::table('seat_info')->where('screen','=',$screen_name)->where('row','=',$rowName)->get();
        $response = array();
        $reservedSeats = $row[0]->reservedSeats;
        $noOfSeats = $row[0]->noOfSeats;
        $aisleSeats = $row[0]->aisleSeats;
        $aisleSeats = explode(",", $aisleSeats);
        $reservedSeats = explode(",", $reservedSeats);
        $unreservedSeats = array();
        for($i = 1; $i<=$noOfSeats; $i++){
            if(!in_array($i, $reservedSeats) && !in_array($i, $aisleSeats)){
                array_push($unreservedSeats, $i);
            }
        } 
        $consecutivesUnreserved = $this->util($unreservedSeats);
        $countArr = array_map('count', $consecutivesUnreserved);
        if(max($countArr)<$x){
            return response()->json('Error: Seats not available', 201);
        }
        $final = array();

        $nearest = $this->getClosest($x, $countArr);
        $final = $consecutivesUnreserved[$nearest];
        if(count($final) == 0){
            return response()->json('Error: Seats not available', 201);
        }
        $response['availableSeats'][$row[0]->row] = $final;

        return response()->json($response, 200);   
    }

    public function reserve(Request $request, $screen_name)
    {
        $this->validate($request, [
            'seats' => 'required'
        ]);
        $seats = $request['seats'];
        $flag = 1;
        foreach($seats as $key=>$info){
            $row = $key;

            $alreadyReserved = DB::table('seat_info')->where('screen','=',$screen_name)->where('row','=',$row)->pluck('reservedSeats');
            if($alreadyReserved[0] != "")
                $alreadyReserved = explode(",", $alreadyReserved);
            else
                $alreadyReserved = array();
            foreach($alreadyReserved as $i){
                $i = (int)preg_replace('/\D/', '', $i);                
                if(in_array($i, $info)){
                    $flag = 0;
                    break;
                }
                array_push($info, $i);
            }
            if($flag){
                $reservedSeats = implode(",", $info);
                DB::statement("UPDATE seat_info SET reservedSeats = '$reservedSeats' WHERE screen = '$screen_name' AND row = '$row'");
            }
        }
        if($flag)
        return response()->json('Success', 200);   
        else
        return response()->json('Error: Already Reserved', 201);
    }

    public function util($array){
        // Sorting
        asort($array);

        $previous = null; 
        $result = array();
        $consecutiveArray = array();

        // Slice array by consecutive sequences
        foreach($array as $number) {
            if ($number == $previous + 1) {
                $consecutiveArray[] = $number;
            } else {
                $result[] = $consecutiveArray;
                $consecutiveArray = array($number);
            }
            $previous = $number;
        }
        $result[] = $consecutiveArray;
        return $result;
    }

    public function getClosest($search, $arr) {
        $closest = null;
        foreach ($arr as $key=>$item) {
           if ($closest === null || abs($search - $closest) > abs($item - $search)) {
              $closest = $item;
              $closestKey = $key;
           }
        }
        return $closestKey;
     }
     
}
?>
