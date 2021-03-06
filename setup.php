<?php
  // Start the session
  require 'vendor/autoload.php';

  $rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
  ]);

  print "Create RDS DB results: \n";
  # print_r($rds);

  $result = $rds->createDBInstance([
    'AllocatedStorage' => 10,
    'DBInstanceClass' => 'db.t2.micro', // REQUIRED
    'DBInstanceIdentifier' => 'mp1-sg', // REQUIRED
    'DBName' => 'customerrecords',
    'Engine' => 'MySQL', // REQUIRED
    'EngineVersion' => '5.6.23',
    'MasterUsername' => 'sandhyagupta',
    'MasterUserPassword' => 'sandhya987',   
    'PubliclyAccessible' => true,
    #'VpcSecurityGroupIds' => ['<string>', ...],
  ]);


  $result = $rds->waitUntil('DBInstanceAvailable',['DBInstanceIdentifier' => 'mp1-sg',]);

  // Create DB Instance Read Replica
  /*$readreplicaresult = $rds->createDBInstanceReadReplica([
    'AutoMinorVersionUpgrade' => true,
    'DBInstanceClass' => 'db.t2.micro',
    'DBInstanceIdentifier' => 'mp1-rr-sg', // REQUIRED
    'PubliclyAccessible' => true,
    'SourceDBInstanceIdentifier' => 'mp1-sg', // REQUIRED
  ]);

  print "Created DB Instance Read Replica: \n"*/

  // Create a table 
  $result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'mp1-sg',
  ]);

  $endpoint = $result['DBInstances'][0]['Endpoint']['Address'];

  $link = mysqli_connect($endpoint,"sandhyagupta","sandhya987","customerrecords") or die("Error " . mysqli_error($link)); 
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  $create_table = 'CREATE TABLE IF NOT EXISTS userdetails  
  (
    id INT NOT NULL AUTO_INCREMENT,
    uname VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    s3rawurl VARCHAR(255) NOT NULL,
    s3finishedurl VARCHAR(255) NOT NULL,    
    jpgfilename VARCHAR(255) NOT NULL,	
    status INT NOT NULL,
    createdat DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id)
  )';

  if (mysqli_query($link, $create_table) === TRUE) {
    printf("Table userdetails successfully created.\n");
  }


?>

