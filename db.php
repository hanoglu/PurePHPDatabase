<!--
PurePHPDatabase is a very minimal database written to use on the limited accessed server.
Copyright (C) 2021  Yusuf K. Hanoğlu

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; version 2
of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
-->

<?php 
ignore_user_abort(true);
set_time_limit(20);
header('Content-Type: text/html; charset=utf-8');

// ***********************************************************************************************
// USER MIGHT WANT TO CHANGE THESE PARAMETERS
// DEFAULT PASSWORD SHOULD BE CHANGED
// LEFT AND RIGHT MARGINS SHOULD BE CHANGED IF NECESSARY
$PASSWORD = "admin";
$MARGIN_LEFT = 13;
$MARGIN_RIGHT = 5;
// ***********************************************************************************************

$data_basenin_adi = $_GET["db"].".php";
$method = $_GET["method"];
$p1 = $_GET["p1"];
$p2 = $_GET["p2"];
$p3 = $_GET["p3"];

$passwd = $_GET["passwd"];
if($data_basenin_adi == "") {

    echo "<h1>Usage</h1>";
    echo "<b1>db.php?db=DB_NAME&passwd=YOUR_PASSWORD&method=DB_FUNCTION&p1=PARAM1&p2=PARAM2&p3=PARAM3</b1>";
    echo "<h2>Available DB_FUNCTIONs</h2>";
    echo "<b1>nameExists : Checks if name 'p1' exists, if exists returns index of name 'p1', if not returns '-1'.</b1><br>";
    echo "<b1>getDataCount : Returns how many datas exists in specific name 'p1', returns 'null' if name 'p1' not exists.</b1><br>";
    echo "<b1>getData : Returns the value of name 'p1's 'p2'th data, returns 'null' if at least one of 'p1' or 'p2' is not defined.</b1><br>";
    echo "<b1>deleteData : Deletes name 'p1's 'p2'th data, if succeed returns '1', if not returns '0'.</b1><br>";
    echo "<b1>deleteName : Deletes name 'p1', if succeed returns '1', if not returns '0'.</b1><br>";
    echo "<b1>addName : Creates name 'p1' in specified DB, if succeed returns '1', if not succeed or name is already exists returns '0'.</b1><br>";
    echo "<b1>addData : Adds a new data 'p2' to name 'p1', if succeed returns '1', if not returns '0'.</b1><br>";
    echo "<b1>setData : Changes the value of 'p2'th data of name 'p1' to new value 'p3', if succeed returns '1', if not returns '0'.</b1><br>";
    echo "<b1>setName : Renames name 'p1' to new name 'p2', if succeed returns '1', if not retruns '0'.</b1><br>";
    echo "<b1>getAllDatabase : Prints all names and corresponding datas of DB.</b1><br>";
    echo "<b1></b1>";
    echo "<h2>Logic of The DB</h2>";
    echo "<b1>There are names under the DB tree and datas under the names. Word 'name' corresponds the 'key' and 'data's corresponds the 'value's of this 'key'. More than one data can be identified for each name.</b1><br>";
    echo "<br><b3>More information about project can be found at <a href='https://github.com/yusufhanoglu/PurePHPDatabase'>Project's Github Page</a></b3>";
    exit();
}

if($passwd !== $PASSWORD){
	echo "<h1>Access Denied</h1>";
    echo "<h3>Erişim Engellendi</h3>";
	exit();
}

$db = new Database();
$db->connect($data_basenin_adi);
$db->configureDatabase();

if($method === "nameExists")
	echo ($db->nameExists($p1));
else if($method === "getDataCount")
	echo ($db->getDataCount($p1));
else if($method === "getData")
	echo ($db->getData($p1,$p2));
else if($method === "deleteData")
	echo ($db->deleteData($p1,$p2));
else if($method === "deleteName")
	echo ($db->deleteName($p1));
else if($method === "addName")
	echo ($db->addName($p1));
else if($method === "addData")
	echo ($db->addData($p1,$p2));
else if($method === "setData")
	echo ($db->setData($p1,$p2,$p3));
else if($method === "setName")
	echo ($db->setName($p1,$p2));
else if($method === "getAllDatabase"){
	echo ("".$db->getAllDatabase());
	
}

$db->disconnect();





class Database{

public $Database_File = "Not Defined Yet";
public $Database_Name = "Not Defined Yet";
public $Data = "Not Defined Yet";
public $Data_Posterior;
public $Database_Edit_Mode = false;
public $Database_Order_Number = -1;
public $Database_Order_Unique = -1;
public $Database_Update_Parameter = -1;
public $Database_Edited = -1;



function connect($database_name){
	
		if($this->Database_Name !== "Not Defined Yet")
			return 0;
		$this->Database_Name = $database_name;
		return $this->updateConnection();
		
}
function updateConnection(){
	
	if($this->Database_Edited != -1){
		if(!$this->isDatabaseUpdated())
			return 1;
	}
	
	
	if($this->Database_Name === "Not Defined Yet"){
		return 0;
	}
	if(!file_exists($this->Database_Name)){
		
		$dosya = $this->Database_Name;
	
		$creator = fopen($dosya, "w"); 
		fwrite($creator,"Creating");
		fclose($creator);
	
	}
	$this->Database_File = fopen($this->Database_Name,"r");

	// $this->Data = fread($this->Database_File,filesize($this->Database_Name));
    $this->Data = fgets($this->Database_File);

	fclose($this->Database_File);
	
	$this->Database_Update_Parameter = $this->fileSystemGetUnique();
	
	$this->configureDatabase();
	
	
	
	
}
function getAllDatabase(){

	$data = "";
	for($i=0;$i<count($this->Data_Posterior);$i++){
		if($i==0)
			$data .= $this->Data_Posterior[$i];
		else
			$data .= "#".$this->Data_Posterior[$i];

	}
	
	
	return $data;
}
function configureDatabase(){
	global $MARGIN_LEFT, $MARGIN_RIGHT;
    // Paddings ang margins should be configured
    $notar = substr($this->Data, $MARGIN_LEFT, -$MARGIN_RIGHT);
	$nameanddata = explode("#",$notar);
	$this->Data_Posterior = $nameanddata;
	
	return 1;
	
}

function isDatabaseUpdated(){
	
	if($this->Database_Update_Parameter != $this->fileSystemGetUnique())
		return 1;
	
	return 0;
}
function editModeOn($edit_mode_on){
	global $onUsingBool;
	global $this_db;
		//txt deki değer d, unique u, unique text değeri q ve order number o olsun
		
	$this->updateConnection();
	
	
	
			//order number her saniye bir artar eğer 1.5 saniye içinde herhangi bir artış olmazsa herhangi bir oturum order numberı ve unique numberı sıfırlar

	
	
	

	if($this->Database_Edit_Mode == true && !$edit_mode_on){
		
		//d--; q++; if(q > 2000000000){q=0;}	ok
		
		$onUsingBool = false;
		usleep(1000000);//bir saniye bekleme
		
		//bunrada hata çıkarsa while içine al
		$this->fileSystemSetOrderNumber(0);
		$this->fileSystemSetUnique(0);
		
		
		
		$this->Database_Order_Unique = -1;//unique number ile işimiz bitti
		$this->Database_Order_Number = -1;//artık uygulama sıradan çıktı
		$this->Database_Edit_Mode = false;
		return 1;
	}
	else if($this->Database_Edit_Mode == false && $edit_mode_on){
		
		$this->Database_Order_Number = 0;//txt belgesindeki değer
		$this->Database_Order_Unique = rand();
		
		

			//edit modeu açmak için uniqueyi sürekli kontrol et eğer unique 0 olursa yeni unique oluştur
			//order numberı sürekli kontrol et eğer 1.5 saniye içinde herhangi bir değişim yoksa hem uniqueyi hem de order numberı sıfırla
			
			while($this->Database_Order_Unique != $this->fileSystemGetUnique()){
				
				usleep(10000);//10 ms bekleme
								
				if($this->fileSystemGetUnique() == 0){
					$this->fileSystemSetUnique($this->Database_Order_Unique);
					break;
					
				}
				
				$oncekiorder = $this->fileSystemGetOrderNumber();
				
				usleep(2000000); //tam olarak 2 saniye bekleme
				
				$sonrakiorder = $this->fileSystemGetOrderNumber();
				
				if($oncekiorder == $sonrakiorder){
					$this->fileSystemSetUnique(0);
					break;
				}
			}
			
		
			if($this->Database_Order_Unique != $this->fileSystemGetUnique()){
				$this->Database_Edit_Mode = true;
				$this->updateConnection();
				$this_db = $this;
				$onUsingBool = true;


				//therad başlatıldığında hata ile karşılaşıyorum ve en önemlisi program
				//çöküyor ve ilerlemiyor ancak herhangi bir hata çıktısı gelmiyor
				//ve uygulama dönmüyor, sayfa yükleniyor ancak bu noktada kopuyor...
				//$thread=new onUsing();//her saniye order numberı bir arttırmalı

				//$thread->start();

		
			}
			else
				return 0;
			
		
			
		return 1;
		
	}
	else 
		return 1;
	
	//	a -> 0 -> 1		0
	//	b -> 1 -> 2		1
	//	c -> 2 -> 3		2
	
	
	
	
}


function nameExists($name){
		$this->updateConnection();

	$donut = -1;
	
	$data = $this->Data_Posterior;
	
	for($i=0;$i<count($data);$i++){
		$row = explode("@",$data[$i]);
		if($row[0] === $name){
			$donut = $i;
			break;
		}
	}
		
	
	
	
	return $donut;
	
}
function getDataCount($name){
	
	$this->updateConnection();
	$data = $this->Data_Posterior;
	$name_index = $this->nameExists($name);
	if($name_index == -1){
		return "null";
	}
	
	
	$row = explode("@",$data[$name_index]);
	return count($row)-1;
	
	
	
	
}
function getData($name, $data_count){
	
		$this->updateConnection();

	
	$data = $this->Data_Posterior;
	$name_index = $this->nameExists($name);
	if($name_index == -1){
		return "null";
	}
	if($this->getDataCount($name) <= $data_count){
		return "null";
	}

	
	$row = explode("@",$data[$name_index]);
	return $row[$data_count + 1];
	
}




function deleteData($name, $data_count){
	
	if(!$this->editModeOn(true)){
		return 0;
	}
	
	$data = $this->Data_Posterior;
	$name_index = $this->nameExists($name);
	if($name_index == -1){
		return 0;
	}
	if($this->getDataCount($name) <= $data_count){
		return 0;
	}
	
	
	$row = explode("@",$data[$name_index]);
	
	$duzenle = "";
	for($i=0;$i<count($row);$i++){
		
		if($i!=0){
			if($i != $data_count+1)
				$duzenle .= "@".$row[$i];
		}
		else
			$duzenle .= $row[$i];
	}
	
	$data[$name_index] = $duzenle;
	$this->Data_Posterior = $data;
	
	return $this->saveDatabase();
	
	
	
	
}
function deleteName($name){
	
	if(!$this->editModeOn(true)){
		return 0;
	}
	
	$data = $this->Data_Posterior;
	$name_index = $this->nameExists($name);
	if($name_index == -1){
		return 0;
	}
	
	
	
	$row = explode("@",$data[$name_index]);
	
	$data[$name_index] = "#Deleted Name#";
	$this->Data_Posterior = $data;
	
	return $this->saveDatabase();
	
}

function addName($name){

	if(!$this->editModeOn(true)){
		return 0;
	}

	$data = $this->Data_Posterior;
	$name_index = $this->nameExists($name);
	if($name_index != -1){
		return 0;
	}
	
		
	$data[count($data)] = $name;
	$this->Data_Posterior = $data;
	
	return $this->saveDatabase();
	
	
}
function addData($name,$data_value){
	
	if(!$this->editModeOn(true)){
		return 0;
	}
	
	$data = $this->Data_Posterior;
	$name_index = $this->nameExists($name);
	if($name_index == -1){
		return 0;
	}
	
	
	$row = explode("@",$data[$name_index]);
	
	
	$data[$name_index] .= "@".$data_value;
	$this->Data_Posterior = $data;
	
	return $this->saveDatabase();
	
	
	
	
}
function setData( $name,  $data_count,  $data2){
	
	if(!$this->editModeOn(true)){
		return 0;
	}
	
	$data = $this->Data_Posterior;
	$name_index = $this->nameExists($name);
	if($name_index == -1){
		return 0;
	}
	if($this->getDataCount($name) <= $data_count){
		return 0;
	}
	
	
	$row = explode("@",$data[$name_index]);
	
	$duzenle = "";
	for($i=0;$i<count($row);$i++){
		
		if($i!=0){
			if($i != $data_count+1)
				$duzenle .= "@".$row[$i];
			else
				$duzenle .= "@".$data2;
		}
		else
			$duzenle .= $row[$i];
	}
	
	$data[$name_index] = $duzenle;
	$this->Data_Posterior = $data;
	
	return $this->saveDatabase();
	
	
}
function setName( $name,  $new_name){
	
	if(!$this->editModeOn(true)){
		return 0;
	}
	
	$data = $this->Data_Posterior;
	$name_index = $this->nameExists($name);
	if($name_index == -1){
		return 0;
	}
	
	
	$row = explode("@",$data[$name_index]);
	
	$duzenle = "";
	for($i=0;$i<count($row);$i++){
		
		if($i!=0){
				$duzenle .= "@".$row[$i];
		}
		else
			$duzenle .= $new_name;
	}
	
	$data[$name_index] = $duzenle;
	$this->Data_Posterior = $data;
	
	return $this->saveDatabase();	
	
}


function saveDatabase(){

	$myfile = fopen($this->Database_Name, "w") or die("Unable to open file!");
	$txt = '<?php $data="';
	$ilk = 0;
	for($i=0;$i<count($this->Data_Posterior)+1;$i++){ // Other Margin +1
		if($i == $ilk){
			if($this->Data_Posterior[$i] === "#Deleted Name#"){
				$ilk++;continue;
			}
			$txt .= $this->Data_Posterior[$i];
		}
		else{
			if($this->Data_Posterior[$i] === "#Deleted Name#"){
				continue;
			}
			$txt .= "#".$this->Data_Posterior[$i];
		}
		
	}
	$txt .= '";?>';
	
	
	fwrite($myfile, $txt);
	fclose($myfile);
	
	
	$this->editModeOn(false);
	
	//bu alanda kayıt yaparken data_posterior u kullanmalısın
	//çünkü data değişmiyor orjinal haliyle duruyor
	return 1;
}
function disconnect(){
	
	exit(0);
	
}






function fileSystemGetUnique(){
	
	if($this->Database_Name === "Not Defined Yet"){
		return 0;
	}
	$dosya = $this->Database_Name.".unique.txt";
	
	if(!file_exists($dosya)){
		$this->fileSystemSetUnique(0);
	}
	$file = fopen($dosya,"r");
	
	// $data = fread($file,filesize($dosya));
    $data = fgets($file);

	fclose($file);
		
	return $data;
}

function fileSystemSetUnique( $number){//q

	if($this->Database_Name === "Not Defined Yet"){
		return 0;
	}
	$dosya = $this->Database_Name.".unique.txt";
	
	$creator = fopen($dosya, "w"); 
	fwrite($creator,$number);
	fclose($creator);
	
	return 1;
}
function fileSystemGetOrderNumber(){//d
	
	if($this->Database_Name === "Not Defined Yet"){
		return 0;
	}
	$dosya = $this->Database_Name.".order.txt";
	
	if(!file_exists($dosya)){
		$this->fileSystemSetOrderNumber(0);
	}
	$file = fopen($dosya,"r");
	
	// $data = fread($file,filesize($dosya));
    $data = fgets($file);

	fclose($file);
		
	return $data;
	
	
	
}
function fileSystemSetOrderNumber($number){
if($this->Database_Name === "Not Defined Yet"){
		return 0;
	}
	$dosya = $this->Database_Name.".order.txt";
	
	$creator = fopen($dosya, "w"); 
	fwrite($creator,$number);
	fclose($creator);
	
	return 1;



}





}












 $onUsingBool = false;
 $this_db;




class onUsing extends Thread{

public function run(){
	global $onUsingBool;
	global $this_db;
	
	while($onUsingBool){
		//order number her saniye bir artar eğer 1.5 saniye içinde herhangi bir artış olmazsa herhangi bir oturum order numberı ve unique numberı sıfırlar
		
		$this_db->fileSystemSetOrderNumber($this_db->fileSystemGetOrderNumber()+1);
		
		
		usleep(1000000);// 1 saniye bekleme
	}
		$this_db->fileSystemSetOrderNumber(0);
		$this_db->fileSystemGetUnique(0);
	
	
}	
	



}

 ?>
