<?php

error_reporting(E_ALL);
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);

        $servername = "localhost";
        $username = "kundkontaker";
        $password = "eiEhCIyL@3qwumNSF";
        $dbname = "kundkontaker";


        function unique_multidim_array($array_unique, $array_actual, $index) { 
		   	
                // To create a uniques addresses array from 3.4M data to 300k 

                $address = array_column($array_actual, 'address');
                array_multisort($address, SORT_ASC, $array_actual);
                $pluked_address = array_column($array_actual, 'address');

                foreach($array_unique as $key => $val) { 
			    
                        $found_index = array_search($val, $pluked_address, true);

		    	$temp_array[] = array(
		      			'address' => $val , 
					'living_type_string' => $array_actual[$found_index]['living_type_string'],
					'living_type' => $array_actual[$found_index]['living_type']
			             );


			$found_index = 0;

		} 
			
		return $temp_array;

	}

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT id, living_type_string, living_type, address, post_number FROM `customer_contacts_backup_4` where living_type IN (124959,124960,145340) and address is not null";
        $result = $conn->query($sql);

        //3.4M

        $count = 0;
        
        // echo $result->num_rows;die();

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {

                	$explode = explode(' lgh', $row['address']);

                	// Merging postal code with address to get unique values on the basis of address and postal 
		            $row['address'] = $explode[0] . ' --- ' . $row['post_number'];

		            $main_array[] = $row;
                	
                }

                $unique = array_unique(array_column($main_array, 'address'));

                // echo count($unique);die();

	        $unique_addresses = unique_multidim_array($unique, $main_array, 'address');

                echo 'Unique count   => ' . count($unique_addresses) . '                 ';

                die();

                // print_r($unique_addresses);die();

                foreach ($unique_addresses as $key => $value) {
                	
        		if($value['living_type'] == 124959)
        			$living_type_string = 'Bostadsr??tt';
                        
                        else if($value['living_type'] == 124960)
        			$living_type_string = 'Hyresr??tter';
                        
                        else if($value['living_type'] == 145340)
        			$living_type_string = '??vrig';

        		else
        			$living_type_string = '';


                        
                        $living_type = $value['living_type'];
                        
                        // Removing postal code from address
                        $clean_address = explode(' ---', $value['address']);
		            	$address = $clean_address[0];

                        
                        $sql = "Update customer_contacts set living_type = '".$living_type."', living_type_string = '". $living_type_string ."' where `address` LIKE '%". $address . "%' and address_type = 1321 and living_type is null";
                        // die();

                        $conn->query($sql);


                        $count ++;

                }

        } else {

                echo "0 results";

        }

        $conn->close();


// update customer_contacts AS c1, customer_contacts_backup_4 AS c2 
// set c1.living_type=(
// 					select living_type from customer_contacts_backup_4 
// 					where customer_contacts_backup_4.address=c1.address 
// 					and customer_contacts_backup_4.living_type IS NOT NULL LIMIT 1
// ) 
// where c1.id > 10 and c1.id <= 500000;



?>