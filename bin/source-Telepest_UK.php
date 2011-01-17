<?php
//this file is designed to be used as an include that is part of a loop.
//If a valid match is found, it should give $caller_id a value
//available variables for use are: $thenumber
//retreive website contents using get_url_contents($url);

//configuration / display parameters
//The description cannot contain "a" tags, but can contain limited HTML. Some HTML (like the a tags) will break the UI.
$source_desc = "http://www.telepest.co.uk - A datasource devoted to identifying telemarketers. All information on this site is submitted by users. The operators of Telepest make no claims whatsoever regarding its accuracy or reliability.";
$source_param = array();
//$source_param['Username']['desc'] = 'Your user account Login on the Telepest.co.uk web site.';
//$source_param['Username']['type'] = 'text';
//$source_param['Password']['desc'] = 'Your user account Password on the Telepest.co.uk web site.';
//$source_param['Password']['type'] = 'password';
//$source_param['Report_Back']['desc'] = 'If a valid caller id name is found, provide it back to Telepest for their database.';
//$source_param['Report_Back']['type'] = 'checkbox';
$source_param['Get_Caller_ID_Name']['desc'] = 'Use Telepest.co.uk for caller id name lookup.';
$source_param['Get_Caller_ID_Name']['type'] = 'checkbox';
$source_param['Get_Caller_ID_Name']['default'] = 'on';
$source_param['Get_SPAM_Score']['desc'] = 'Use Telepest.co.uk for spam scoring.';
$source_param['Get_SPAM_Score']['type'] = 'checkbox';
$source_param['Get_SPAM_Score']['default'] = 'on';

//run this if the script is running in the "get caller id" usage mode.
//and only if either run mode is required
if(($usage_mode == 'get caller id') && (($run_param['Get_SPAM_Score'] == 'on') || ($run_param['Get_Caller_ID_Name'] == 'on')))
{
	$number_error = false;
      if($debug)
	{
		print "Searching Telepest.co.uk ... ";
	}
	

	//check for the correct 8 ~ 13 digits in UK phone numbers. leading digits before the 44 international code will be ignored.
	// check international format
	if (strlen($thenumber) > 10)
	{
		if (substr($thenumber,-11,2) == 44)
		{
			$thenumber = substr($thenumber, -11);
		}
		else
		{
			if (strlen($thenumber) > 11)
			{
				if (substr($thenumber,-12,2) == 44)
				{
				$thenumber = substr($thenumber, -12);
				}

			}
			else
			{
				$number_error = true;
			}
		}
      }
	//check for 11 digits national format.
      if(strlen($thenumber) ==11)
      {
       	if (substr($thenumber,-11,1) == 0)
		{
		$number_error = false;
		}
      }			

      if(strlen($thenumber) < 8)
	{
		$number_error = true;

	}	

	
	if(!$number_error)
	{
		// Convert 441xxx to 01xxx if delivered in International Format
                $thenumber = (substr($thenumber,0,2) == 44) ? "0".substr($thenumber,2) : $thenumber;
		$prefix2 = substr($thenumber,0,5);

		// Initialise $validSTD and $validNGN
		$validSTD = false;
		$validNGN = false;

		if($prefix2 < 3000)
		{
			// Check for valid UK STD
			$STD = array(
				"01130", "01131", "01132", "01133", "01140", "01141", "01142", "01143", "01150", "01151",
				"01158", "01159", "01160", "01161", "01162", "01163", "01170", "01171", "01173", "01179",
				"01180", "01181", "01183", "01189", "01200", "01202", "01204", "01205", "01206", "01207",
				"01208", "01209", "01210", "01211", "01212", "01213", "01214", "01215", "01216", "01217",
				"01218", "01219", "01223", "01224", "01225", "01226", "01228", "01229", "01233", "01234",
				"01235", "01236", "01237", "01239", "01241", "01242", "01243", "01244", "01245", "01246",
				"01248", "01249", "01250", "01252", "01253", "01254", "01255", "01256", "01257", "01258",
				"01259", "01260", "01261", "01262", "01263", "01264", "01267", "01268", "01269", "01270",
				"01271", "01273", "01274", "01275", "01276", "01277", "01278", "01279", "01280", "01282",
				"01283", "01284", "01285", "01286", "01287", "01288", "01289", "01290", "01291", "01292",
				"01293", "01294", "01295", "01296", "01297", "01298", "01299", "01300", "01301", "01302",
				"01303", "01304", "01305", "01306", "01307", "01308", "01309", "01310", "01311", "01312",
				"01313", "01314", "01315", "01316", "01317", "01318", "01320", "01322", "01323", "01324",
				"01325", "01326", "01327", "01328", "01329", "01330", "01332", "01333", "01334", "01335",
				"01337", "01339", "01340", "01341", "01342", "01343", "01344", "01346", "01347", "01348",
				"01349", "01350", "01352", "01353", "01354", "01355", "01356", "01357", "01358", "01359",
				"01360", "01361", "01362", "01363", "01364", "01366", "01367", "01368", "01369", "01371",
				"01372", "01373", "01375", "01376", "01377", "01379", "01380", "01381", "01382", "01383",
				"01384", "01386", "01387", "01388", "01389", "01392", "01394", "01395", "01397", "01398",
				"01403", "01404", "01405", "01406", "01407", "01408", "01409", "01410", "01411", "01412",
				"01413", "01414", "01415", "01416", "01417", "01418", "01419", "01420", "01422", "01423",
				"01424", "01425", "01427", "01428", "01429", "01430", "01431", "01432", "01433", "01434",
				"01435", "01436", "01437", "01438", "01439", "01440", "01442", "01443", "01444", "01445",
				"01446", "01449", "01450", "01451", "01452", "01453", "01454", "01455", "01456", "01457",
				"01458", "01460", "01461", "01462", "01463", "01464", "01465", "01466", "01467", "01469",
				"01470", "01471", "01472", "01473", "01474", "01475", "01476", "01477", "01478", "01479",
				"01480", "01481", "01482", "01483", "01484", "01485", "01487", "01488", "01489", "01490",
				"01491", "01492", "01493", "01494", "01495", "01496", "01497", "01499", "01501", "01502",
				"01503", "01505", "01506", "01507", "01508", "01509", "01510", "01511", "01512", "01513",
				"01514", "01515", "01516", "01517", "01518", "01519", "01520", "01522", "01524", "01525",
				"01526", "01527", "01528", "01529", "01530", "01531", "01534", "01535", "01536", "01538",
				"01539", "01540", "01542", "01543", "01544", "01545", "01546", "01547", "01548", "01549",
				"01550", "01551", "01553", "01554", "01555", "01556", "01557", "01558", "01559", "01560",
				"01561", "01562", "01563", "01564", "01565", "01566", "01567", "01568", "01569", "01570",
				"01571", "01572", "01573", "01575", "01576", "01577", "01578", "01579", "01580", "01581",
				"01582", "01583", "01584", "01586", "01588", "01590", "01591", "01592", "01593", "01594",
				"01595", "01597", "01598", "01599", "01600", "01603", "01604", "01606", "01608", "01610",
				"01611", "01612", "01613", "01614", "01615", "01616", "01617", "01618", "01619", "01620",
				"01621", "01622", "01623", "01624", "01625", "01626", "01628", "01629", "01630", "01631",
				"01633", "01634", "01635", "01636", "01637", "01638", "01639", "01641", "01642", "01643",
				"01644", "01646", "01647", "01650", "01651", "01652", "01653", "01654", "01655", "01656",
				"01659", "01661", "01663", "01664", "01665", "01666", "01667", "01668", "01669", "01670",
				"01671", "01672", "01673", "01674", "01675", "01676", "01677", "01678", "01680", "01681",
				"01683", "01684", "01685", "01686", "01687", "01688", "01689", "01690", "01691", "01692",
				"01694", "01695", "01697", "01698", "01700", "01702", "01704", "01706", "01707", "01708",
				"01709", "01721", "01722", "01723", "01724", "01725", "01726", "01727", "01728", "01729",
				"01730", "01731", "01732", "01733", "01736", "01737", "01738", "01740", "01743", "01744",
				"01745", "01746", "01747", "01748", "01749", "01750", "01751", "01752", "01753", "01754",
				"01756", "01757", "01758", "01759", "01760", "01761", "01763", "01764", "01765", "01766",
				"01767", "01768", "01769", "01770", "01771", "01772", "01773", "01775", "01776", "01777",
				"01778", "01779", "01780", "01782", "01784", "01785", "01786", "01787", "01788", "01789",
				"01790", "01792", "01793", "01794", "01795", "01796", "01797", "01798", "01799", "01803",
				"01805", "01806", "01807", "01808", "01809", "01821", "01822", "01823", "01824", "01825",
				"01827", "01828", "01829", "01830", "01832", "01833", "01834", "01835", "01837", "01838",
				"01840", "01841", "01842", "01843", "01844", "01845", "01847", "01848", "01851", "01852",
				"01854", "01855", "01856", "01857", "01858", "01859", "01862", "01863", "01864", "01865",
				"01866", "01869", "01870", "01871", "01872", "01873", "01874", "01875", "01876", "01877",
				"01878", "01879", "01880", "01882", "01883", "01884", "01885", "01886", "01887", "01888",
				"01889", "01890", "01892", "01895", "01896", "01899", "01900", "01902", "01903", "01904",
				"01905", "01908", "01909", "01912", "01913", "01914", "01915", "01920", "01922", "01923",
				"01924", "01925", "01926", "01928", "01929", "01931", "01932", "01933", "01934", "01935",
				"01937", "01938", "01939", "01942", "01943", "01944", "01945", "01946", "01947", "01948",
				"01949", "01950", "01951", "01952", "01953", "01954", "01955", "01957", "01959", "01962",
				"01963", "01964", "01967", "01968", "01969", "01970", "01971", "01972", "01974", "01975",
				"01977", "01978", "01980", "01981", "01982", "01983", "01984", "01985", "01986", "01988",
				"01989", "01992", "01993", "01994", "01995", "01997", "02030", "02031", "02032", "02033", 
				"02034", "02035", "02036", "02037", "02038", "02039", "02070", "02071", "02072", "02073",
				"02074", "02075", "02076", "02077", "02078", "02079", "02080", "02081", "02082", "02083",
				"02084", "02085", "02086", "02087", "02088", "02089", "02380", "02392", "02476", "02820",
				"02821", "02825", "02827", "02828", "02829", "02830", "02837", "02838", "02840", "02841",
				"02842", "02843", "02844", "02866", "02867", "02868", "02870", "02871", "02877", "02879",
				"02880", "02881", "02882", "02885", "02886", "02887", "02889", "02890", "02891", "02892",
				"02893", "02894", "02897", "02900"
			);
		
			if(in_array($prefix2, $STD))
			{
				$validSTD = true;
			}
		}
		else
		{	
			// Check for valid UK NGN
			$NGN = array(
				"03000", "03001", "03002", "03003", "03004", "03005", "03006", "03007", "03008", "03009",
				"03440", "03441", "03442", "03443", "03444", "03445", "03446", "03447", "03448", "03449",
				"03450", "03451", "03452", "03453", "03454", "03455", "03456", "03457", "03458", "03459",
				"03700", "03701", "03702", "03703", "03704", "03705", "03706", "03707", "03708", "03709",
				"03710", "03711", "03712", "03713", "03714", "03715", "03716", "03717", "03718", "03719",
				"05000", "05001", "05002", "05003", "05004", "05005", "05006", "05007", "05008", "05009",
				"08000", "08001", "08002", "08003", "08004", "08005", "08006", "08007", "08008", "08009",
				"08440", "08441", "08442", "08443", "08444", "08445", "08446", "08447", "08448", "08449",
				"08450", "08451", "08452", "08453", "08454", "08455", "08456", "08457", "08458", "08459",
				"08700", "08701", "08702", "08703", "08704", "08705", "08706", "08707", "08708", "08709",
				"08710", "08711", "08712", "08713", "08714", "08715", "08716", "08717", "08718", "08719",
				"04088"
			);

			if(in_array($prefix2, $NGN))
			{
				$validNGN = true;
			}
		}
	}
	
	if(!$validSTD && !$validNGN)
	{
		$number_error = true;
	}
	
	if($number_error)
	{
		if($debug)
		{
			print "Skipping Source - Non UK STD / NGN number: ".$thenumber."<br>\n";
		}
	}
	else
	{
		$url = "http://www.telepest.co.uk/$thenumber";
		$value = get_url_contents($url);
		
		$start = strpos($value, $thenumber.' is not in our database of possible telepests');
		if($start >0)
		{
			$caller_id=''; // Not a telepest
			if($debug)
			{
				print "not found<br>\n";
			}
		}
		else
		{
			$start = strpos($value, $thenumber.' has been reported as a possible telepest');
			if($start >0)
			{
				if($run_param['Get_SPAM_Score'] == 'on')
				{
					$spam = true;	// Reported as a telepest
					if($debug)
					{
						print "SPAM caller<br>\n";
					}
				}
				if($run_param['Get_Caller_ID_Name'] == 'on')
				{
					if($debug)
					{
						print "Looking up CNAM ... ";
					}
					$start = strpos($value, 'We have no information on the ownership of this number.');
					if($start == false)
					{
						$start = strpos($value, 'According to reports, this number is most likely to belong to');
						$value = substr($value,$start+62);
						$end = strpos($value,'</p>');
						$value = substr($value,0, $end);
						$caller_id = strip_tags($value);
					}
					else
					{
						$caller_id = '';	// Should leave blank ?
					}
				}
			}
		}
	}
}
if($usage_mode == 'post processing')
{
	//return the value back to Telepest if the user has enabled it and the result didn't come from cache. This will truncate the string to 15 characters
	if((($winning_source != 'Telepest_UK') && ($first_caller_id != '') && ($spam) && (isset($run_param['Report_Back'])) && ($run_param['Report_Back'] == 'on')))
	{
	$reportbacknow = true;
	}	
	else
	{
	$reportbacknow = false;
	}	
	if ($reportbacknow) 
	{
//		$url = "http://telepest.co.uk/handlers/pestreport.php?action=\"File Report\"&name=".$source_param['Username']."&pass=".$source_param['Password']."&phoneNumber=$thenumber&date=".date('Y-m-d')."&callerID=".urlencode(substr($first_caller_id,0,15));
		$value = get_url_contents($url);
		if($debug)
		{
			$st_success = strstr($value, "success");
			$st_error = strstr($value, "errorMsg");
			$success = substr($st_success,8,1);
			$error = substr($st_error,9);
			if($success=='1')
			{
				print "Success. Reported SPAM caller back to Telepest_UK.co.<br>\n<br>\n";
			}
			else
			{
				print "Failed reporting back to Telepest_UK.co. with error message: ".$error.".<br>\n<br>\n";
			}
		}
	}
}
?>

