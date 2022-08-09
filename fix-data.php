<?php

error_reporting(E_ALL);
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);

        $servername = "localhost";
        $username = "kundkontaker";
        $password = "eiEhCIyL@3qwumNSF";
        $dbname = "kundkontaker";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT id, living_type_string, mobile, living_type, address FROM `customer_contacts_backup_4` where living_type IN (124959,124960,145340) and id = 6530485";
        $result = $conn->query($sql);

        //3.4M

        $count = 0;


        // Nix numbers array
        $nix_numbers = [];
        $file = fopen("data/nix_numbers.txt", "r") or die("Unable to open file 3");
                while (($input = fgets($file)) !== false) {
                        $nix_numbers[] = $input;
        }

        
        // echo $result->num_rows;die();

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {

                        $living_type_string = $row['living_type_string'];
                        $living_type = $row['living_type'];
                        $address = $row['address'];



                        // Remove duplicates
            //             foreach(array_unique($addresses) as $key => $address){

				        //     $input  = trim($address);

				        //     $result = explode(' lgh', $address);

				        //     if(strlen(trim($result[0])) <= 2)
				        //         continue;

				        //     $unique_addresses[] = $result[0];

				        // }






                        if($row['mobile']){
                        	$number = (int)substr((string)$row['mobile'], 2);
							if(in_array($number, $nix_numbers))
								$nix = 1;
							else
								$nix = 0;

                        }

                        $sql = "Update customer_contacts set living_type = '".$living_type."', living_type_string = '". $living_type_string ."', nix = '". $nix ."' where address = '". $address . "' where address_type = 1321 and living_type is not null";

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