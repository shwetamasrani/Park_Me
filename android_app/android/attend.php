<?php
/* include 'DatabaseConfig.php';
$con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
echo "hello";
$qry="update 1test set atd='NUll' where en_no='150320107011'";
$sql1=mysqli_query($con,$qry);

mysqli_close($con);*/
include 'DatabaseConfig.php';
include 'allocate.php';
$con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

 if($_SERVER['REQUEST_METHOD']=='POST')
 {
  $id = $_POST['id'];
 $mail= $_POST['mail'];
   
 
 $Sql_Query = "select * from slot_tbl where email = $id";
 
 $check = mysqli_fetch_array(mysqli_query($con,$Sql_Query));
 
 if(isset($check))
 {
 
// $qry="update 1test set atd='P' where en_no=$id";
        $qry6="select * from slot_tbl where email='$id' and c='1' ";
        $res6=mysqli_fetch_array(mysqli_query($con,$qry6));
        if(isset($res6))
        {
            
			/*$req_time=mysqli_query($con,"select startT from slot_tbl where email='".$id."'");
			$exit_time=mysqli_query($con,"select etime from slot_tbl where email='".$id."'");
			$charge=($exit_time-$req_time)*10;
			$qry8="update transaction set charge='".$charge."' where email='".$id."'";
			mysqli_query($con,$qry8);*/
			$qry7="update slot_tbl set c='2' , eTime=CURRENT_TIME() where email='".$id."'";
			mysqli_query($con,$qry7);
			
			$res8=mysqli_fetch_array(mysqli_query($con,"select startT,eTime from slot_tbl where email='".$id."'"));
			
			$charge=(($res8['eTime']-$res8['startT'])+5.5)*10;
			
			$qry8="update transaction set charge='$charge' where contact='".$id."'";
			mysqli_query($con,$qry8);
			
			
        }
        else
        {
        $qry="update slot_tbl set c='1' , cTime=CURRENT_TIME() where email='".$id."'";
        mysqli_query($con,$qry);
        $qry1="select build, startT, endT from slot_tbl where email='$id'";
        $res=mysqli_query($con,$qry1);
        $resu=mysqli_fetch_array($res);
         
        /* $qry2="insert into transaction(t_id,p_id) values('','$id')";
        $pres2=mysqli_query($con,$qry2);

        //echo allocat($resu['startT'],$resu['endT'],$resu['build'],$id);*/
    
   
    //Allocation    
    $start_time = $resu['startT'];
	$end_time = $resu['endT'];
	$build = $resu['build'];
	$contact=$id;

	$result = mysqli_query($con ,"SELECT * from building where build = '$build'") or die("Failed to query database1".mysql_error());
	$row = mysqli_fetch_array($result);
     
	if(is_array($row)) {
		//$_SESSION["u_id"] = $row['u_id'];
		$b_id =$row['b_id'];
		$capacity = $row['capacity']; //500
		$floors = $row['floors'];    //2
		$zones = $row['zones'];      //50
	}
	$floor_cap=$capacity/$floors; //250
	$zone_per_floor=$zones/$floors; //25
	$zone_cap=$floor_cap/$zone_per_floor; //10
	//temp variables
	$result = mysqli_query($con ,"SELECT * from transaction where p_id LIKE CONCAT('0',$b_id,'%') order by t_id desc Limit 1") or die("Failed to query database1".mysql_error());
	$row = mysqli_fetch_array( $result);
	if(isset($row['t_id'])!=0) {
		//$_SESSION["u_id"] = $row['u_id'];
		$p_id = $row['p_id'];
		
		$bid=substr($p_id,0,2);
		$fid=substr($p_id,2,2);
		$zid=substr($p_id,4,2);
		$sid=substr($p_id,6,2);
		
		if($fidr<=$floors){
			
			if($zid<$zone_per_floor)
			{
				
				if($sid<$zone_cap)
				{
					$sid++;
					$new_p_id=$bid.$fid.$zid.'0'.$sid;
				}
				else
				{
					$sid='01';
					$zid++;
					$new_p_id=$bid.$fid.'0'.$zid.$sid;
				}
			}
			else
			{
			    $sid='01';
			    $zid='01';
			    $fid++;
			    $new_p_id=$bid.'0'.$fid.$zid.$sid;
			}
			
		}
		else
		{
		    echo "Parking Full";
		}
	}
	
	else
	{
		$new_p_id='0'.$b_id.'00'.'01'.'01';
	
	}
		$qry=mysqli_query($con,"insert into transaction(t_id,p_id,contact,rs_id,start_time,end_time) VALUES('','$new_p_id','$id','$id','$start_time','$end_time')");
        }    
        echo "Ok";
 
}
    else
    {
        echo "KO";
    }
 
 }
 else
 {
    echo "hut";
 }
mysqli_close($con);

?>
