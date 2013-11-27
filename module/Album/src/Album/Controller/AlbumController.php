<?php

// module/Album/src/Album/Controller/AlbumController.php:
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;          // <-- Add this import
use Album\Model\XlsData;          // <-- Add this import
use Album\Form\AlbumForm;       // <-- Add this import
use Album\Form\UploadForm;       // <-- Add this import
use Album\Form\ExlprepForm;       // <-- Add this import
use Album\Form\ExlprepsubForm;       // <-- Add this import
use Album\Form\Exlprepsub2Form;       // <-- Add this import
use Album\Form\ExlPrepValidator;       // <-- Add this import
use Album\Form\ExlPrepsubValidator;       // <-- Add this import
use Album\Form\ExlPrepcolValidator;       // <-- Add this import
use Zend\Http\Headers;

class AlbumController extends AbstractActionController
{
protected $albumTable;
public function getAlbumTable()
    {
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
        
    public function indexAction()
    {
         return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll(),
        ));
    }

    public function uploadAction()
    {
            $form = new UploadForm('upload-form');
    
            $request = $this->getRequest();
            if ($request->isPost()) {
                    // Make certain to merge the files info!
                    $post = array_merge_recursive(
                                    $request->getPost()->toArray(),
                                    $request->getFiles()->toArray()
                    );
    
                    $form->setData($post);
                    if ($form->isValid()) {
                            $data = $form->getData();
                            // Form is valid, save the form!
//                             return $this->redirect()->toRoute('upload-form/success');
                            return $this->redirect()->toRoute('album');
                    }
            }
    
            return array('form' => $form);

    }
    public function phpexcelreadAction()
    {
            $file_loc = './public/data/appRegex/';
            $myFile = $file_loc . "testFile2.txt";
            $fh = fopen($myFile, 'w') or die("can't open file");
            var_dump($fh);
            $stringData = "Floppy Jalopy2\n";
            fwrite($fh, $stringData);
            $stringData = "Pointy Pinto2\n";
            fwrite($fh, $stringData);
            
            print_r($stringData);
//             $inputFileName = './sampleData/example1.xls';
            $inputFileName = './public/data/uploads/Garda_issues_1015.xls';
            
            //  Read your Excel workbook
            try {
                    var_dump($inputFileName);
                    $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
                    var_dump($inputFileType);
                    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            }
            
            //  Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            
            //  Loop through each row of the worksheet in turn
            for ($row = 1; $row <= $highestRow; $row++){
                    //  Read a row of data into an array
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                    NULL,
                                    TRUE,
                                    FALSE);
                    //  Insert row data array into your database of choice here
            }
                    print_r($rowData);

    }

    public function exlprepAction()
    {
            $form = new ExlprepForm('exlprep');
//             $form = new UploadForm('upload-form');
    
            $request = $this->getRequest();
            if ($request->isPost()) {
                    // Validator
//                     $formValidator = new UploadForm();
                        $x = 10/2;
                        $result = "result is = " . $x . "\n";

                        try { 
                        $formValidator = new ExlPrepValidator();
//                         var_dump($formValidator);
                        }
                        catch (Exception $e) {
                                $excp = "Exception is : " . $e->getMessage() . "\n";
                        }
                        print_r($excp);
//                         print_r($result);
                    $form->setInputFilter($formValidator->getInputFilter());
//                     var_dump($form);
//                     $validator = new \Zend\Validator\File\Extension(array('php', 'exe'));

                    // Make certain to merge the files info!
                    $post = array_merge_recursive(
                                    $request->getPost()->toArray(),
                                    $request->getFiles()->toArray()
                    );
                    $form->setData($post);
                    
                    // Fine moved to new location: considered to be having this option in the filter
                    // BUG: how to handle an array?
                    $files   = $request->getFiles()->toArray();
//                     $file = $fileArr['uploadExl'];
                    print_r($files);
//                     $filter = new \Zend\Filter\File\RenameUpload("/Applications/MAMP/htdocs/myapp/public/data/uploads/");
//                     $filter->setUseUploadName(true);
//                     echo $filter->filter($files['uploadExl']);
//                     print_r($files);

//                     var_dump($form);
                    if ($form->isValid()) {
                            $data = $form->getData();
                            // Form is valid, save the form!
                            print_r("success!\n");
                            print_r($data);
//                             return $this->redirect()->toRoute('album');

                            // Regex pattern from form to write
//                                 $file_loc = '/Applications/MAMP/htdocs/myapp/public/data/appRegex/';
                                $file_loc = './public/data/appRegex/';
                            $myFile = $file_loc . "testFile.txt";
                            $fh = fopen($myFile, 'w') or die("can't open file");
                            $stringData = "Floppy Jalopy\n";
                            fwrite($fh, $stringData);
                            $stringData = "Pointy Pinto\n";
                            fwrite($fh, $stringData);
                            
                            $searchTerms = $data['searchTerm'];
                                print_r($searchTerms);
                            foreach( $searchTerms as $app ) {
                                    $stringData = $app[appName] . ": " . $app[regexPattern] . "\n";
                                    print_r($stringData);
                                    fwrite($fh, $stringData);
                            }
                            fclose($fh);
                    }
                    else {
                            print_r($form->getMessages()); //stringLengthTooLong error will show
                            $data = $form->getData();
                            print_r("fail!\n");
                            print_r($data);
//                             return $this->redirect()->toRoute('album');
//                             var_dump($form->getMessages()); //stringLengthTooLong error will show
//                             print_r($form->getErrors()); //stringLengthTooLong error will show
//                             return $this->redirect()->toRoute('album', array(
//                                             'controller' => 'Album\Controller\Album',
//                                             'action'     => 'python',
//                                 ));
                    }
            }
    
//             $viewModel = new ViewModel();
//             $viewModel->setTerminal(true);
//             return new ViewModel();
            return array('form' => $form);

//                 return new ViewModel(array(
//                     'albums' => $this->getAlbumTable()->fetchAll(),
//                 ));
    }
    

    /*
     * response url to jquery ajax request: 1st From
     */
    public function exlprep3formsAction()
    {
//             $formsub= new Exlprepsub2Form('exldatasub');
            $formsub= new ExlprepsubForm('exldatasub');
            $request = $this->getRequest();
            $response = $this->getResponse();
            $file_loc = './public/data/uploads/';
            
            /*
             * 2nd form post: jQuery ajax request
             */
            if ($request->isPost()){
                    $postData = $request->getFiles()->toArray();
//                     print_r("exlprep3forms \n");
//                     print_r($postData);
	                $formValidator = new ExlPrepsubValidator();
                    $formsub->setInputFilter($formValidator->getInputFilter());
                    $formsub->setData($postData);
//                     print_r("form validating... \n");
                    
//                     $validator = new \Zend\Validator\File\Extension('txt,text');
//                     $myFile = $file_loc . "TmoApps.txt";
//                     $fileName = $file_loc . $postData['uploadTmp']['name'];
// 		            print_r($fileName);
                    if ($formsub->isValid()) {
	                    $data = $formsub->getData();
// 	                    if($validator->isValid($fileName)) {
// 		                    print_r("\nfile extension validated \n");
// 	                    }
// 	                    print_r("form validated \n");
// 	                    print_r($data);
	                    if(isset($postData['uploadTmp'])){
// 	                            print_r("\nexldatasub form submitted!\n");
	                                $myFile = $file_loc . $postData['uploadTmp']['name'];
// 	                                print_r($myFile);
	//                                 move_uploaded_file($postData['tmpUpload']['tmp_name'], $fileName);
	//                             $myFile = $file_loc . "TmoApps.txt";
	//                             $myFile = $file_loc . $postData['uploadTmp'][];
	                            $fh = fopen($myFile, 'r') or die("can't open file");
	                            $regexstr = "";
// 	                            print_r("regex starts\n");
	                            ob_start();
		                        $applist = array();
	                            while ($line = fgets($fh)) {
	                                    $line_conv = str_replace("|", ",", $line);
	                                    $app = explode(":", $line_conv,2);
	                                    $key = trim($app[0]);
	                                    $value = trim($app[1]);
	                                    $applist[$key] = $value;
	                                    $regexstr = $regexstr . $line_conv . "\n"; 
	                            }
	                            fclose($fh);
	                            ob_end_clean();
	//                             echo $regexstr;
// 	                            print_r("exldatasub regex ready\n");
// 	                            print_r($regexstr);
	                    }
                        $response->setContent(\Zend\Json\Json::encode($applist, true));
			            return $response;
                    } 
                    else{
// 			            return array(
//                             'formsub' => $formsub
// 			            );
						$err_mes1 = "file name not validated";
						return $err_mes1;
                    }
            }
    }

    /*
     * defunct: TODO
     */
    public function appList(){
                                $file_loc = './public/data/uploads/';
                            $myFile = $file_loc . "TmoApps.txt";
                            $fh = fopen($myFile, 'r') or die("can't open file");
                            $regexstr = "";
//                             print_r("regex starts\n");
                            
                        $applist = array();
                            while ($line = fgets($fh)) {
                                    // <... Do You work with the line ...>
                                    $line_conv = str_replace("|", ",", $line);
                                    $app = explode(":", $line_conv,2);
                                    $key = trim($app[0]);
                                    $value = trim($app[1]);
                                    $applist[$key] = $value;
                                    $regexstr = $regexstr . $line_conv . "\n"; 
                            }
                            fclose($fh);
                            return $applist;
    }
                            

    /*
     * response url to jquery ajax request: 2nd main From
     */
    public function exlprep4formsAction()
    {
            // fjord_mamp
            $form = new ExlprepForm('exldata');
//             $form = new UploadForm('upload-form');
//             print_r("exlprep2forms \n");
            $request = $this->getRequest();
            $response = $this->getResponse();

            if ($request->isPost()) {
//                     $postData = $request->getFiles()->toArray();
                    $postData = array_merge_recursive(
//                                     $request->getPost()->toArray()
                                    $request->getPost()->toArray(),
                                    $request->getFiles()->toArray()
                    );
//                     print_r($postData);
//                     print_r("isPost in exlprep4forms\n");
                    
                    /*
                     * validation for taskName, regexPattern.
                     */
                    $formValidator = new ExlPrepValidator();
                    $inputfilter = $formValidator->getInputFilter();
                    $form->setInputFilter($formValidator->getInputFilter());
                    $form->setData($postData);
                    if ($form->isValid()) {
	                    $data = $form->getData();
// 		                print_r("\nform validated \n");
// 		                print_r($data);
		                $colValidator = new ExlPrepcolValidator();
		                $colfilter = $colValidator->getInputFilter();
		                /*
		                 * validation for colletion fieldset: appName, regexPattern.
		                 */
	                    if(isset($postData['searchTerm'])){
	                    	$formvalid = true;
	                    	$searchTerms = $postData['searchTerm'];
                        	foreach( $searchTerms as $app ) {
	                        	$stringData = $app[appName] . ": " . $app[regexPattern] . "\n";
	                        	$colData = array($app[appName], $app[regexPattern]);
// 	                        	print_r($app);
		                    	$colfilter->setData($app);
		                    	if ($colfilter->isValid()) {
// 		                    		print_r("collection fieldset validated\n");
		                    	}
		                    	else{
		                    		$formvalid = false;
		                    		print_r("collection fieldset not validated\n");
		                    	}
                        	}
		                    
	                    }        	/*
		                    	 * Finally all fields are validated
		                    	 */
		                    if($formvalid) {
// 		                    	print_r("All FIELDS VALIDATED\n");
                                // Regex pattern from form to write
	                            $file_loc = './public/data/appRegex/';
	                            /*
	                             * template file
	                             */
                                $myFile = $file_loc . "testFile_1.txt";
                                $fh = fopen($myFile, 'w') or die("can't open file");
                                $searchTerms = $data['searchTerm'];
//                                 print_r($searchTerms);
                                foreach( $searchTerms as $app ) {
                                			$org_pattern = $app[regexPattern];
                                			/*
                                			 * ',' replaced by '|' for python regex search
                                			 */
                                			$conv_pattern = preg_replace("/,/", "|", $org_pattern);
                                            $stringData = $app[appName] . ": " . $conv_pattern . "\n";
//                                             $stringData = $app[appName] . ": " . $app[regexPattern] . "\n";
//                                             print_r($stringData);
                                            fwrite($fh, $stringData);
                                }
                                fclose($fh);
                                    
                                    /*
                                     * PHP python system call:
                                     */
                                	$exlFile = $data['uploadExl']['tmp_name'];
							        $appProc = 'python ./public/python/appPattern.py ' . $myFile . " ". $exlFile;
							//         exec($appProc, $output, $return);
							//         echo "Dir returned $return, and output: $output\n";
// 							        $exe_status = system($appProc, $return);
							        exec($appProc, $exe_status, $return);
// 							        echo "Dir returned $return, and output:\n";
// 							        print_r("exe_status\n");
// 							        print_r($exe_status);
							        $py_result = json_decode($exe_status[0], true);
// 							        var_dump($py_result);
							        /*
							         * PHP system call non-zero exit status: command failed to execute
							         */
							        if($return){
							        	print_r("python execution failed\n");
							        }

// 							        print_r($exlFile);
							        $exldata = new XlsData($exlFile);
							        $exldata->read_rows();
							        $row_arr = $exldata->getRowArr();
							        $not_classified = $exldata->not_classified_rows($py_result);
							        $sel_cols = array(3, 4, 7, 8, 10, 11 );
							        $mod_rows = $exldata->get_selected_cols($sel_cols, $not_classified);
// 							        print_r($row_arr);
// 							        print_r("size of not classified" . sizeof($not_classified));
// 							        var_dump($not_classified);
// 							        print_r("var_dump ended");
// 									print_r("mod_rows");
// 							        print_r($mod_rows);
// 		                    		print_r("\nNew spreadsheet is created!!!\n");
		                    	}
		                    	else{
		                    		print_r("Search Table input error\n");
		                    	}
	                    $response->setContent(\Zend\Json\Json::encode($mod_rows, true));
// 	                    $response->setContent($row_arr);
                    }
                    else{
                    	$inputfilter->setData($postData);
                    	$err_data = $inputfilter->getValidInput();
//                     	print_r($err_data);
//                     	$response->setContent($err_data);
                    	
                    }
	                    return $response;
            }
    
    }
    
    public function exlprep2formsAction()
    {
            // fjord_mamp
            $form = new ExlprepForm('exldata');
            $formsub = new ExlprepsubForm('exldatasub');
//             $form = new UploadForm('upload-form');
//             print_r("exlprep2forms \n");
    
            $request = $this->getRequest();
            if ($request->isPost()) {
//                     $postData = $request->getFiles()->toArray();
                    $postData = array_merge_recursive(
                                    $request->getPost()->toArray(),
                                    $request->getFiles()->toArray()
                    );
                    print_r($postData);
                    print_r("isPost in exlprep2forms\n");
                    
                    /*
                     * validation for taskName, regexPattern.
                     */
                    $formValidator = new ExlPrepValidator();
                    $inputfilter = $formValidator->getInputFilter();
                    $form->setInputFilter($formValidator->getInputFilter());
                    $form->setData($postData);
                    if ($form->isValid()) {
	                    $data = $form->getData();
		                print_r("\nform validated \n");
		                print_r($data);
		                $colValidator = new ExlPrepcolValidator();
		                $colfilter = $colValidator->getInputFilter();
		                /*
		                 * validation for colletion fieldset: appName, regexPattern.
		                 */
	                    if(isset($postData['searchTerm'])){
	                    	$formvalid = true;
	                    	$searchTerms = $postData['searchTerm'];
                        	foreach( $searchTerms as $app ) {
	                        	$stringData = $app[appName] . ": " . $app[regexPattern] . "\n";
	                        	$colData = array($app[appName], $app[regexPattern]);
// 	                        	print_r($app);
		                    	$colfilter->setData($app);
		                    	if ($colfilter->isValid()) {
		                    		print_r("collection fieldset validated\n");
		                    	}
		                    	else{
		                    		$formvalid = false;
		                    		print_r("collection fieldset not validated\n");
		                    	}
                        	}
		                    	
		                    	/*
		                    	 * Finally all fields are validated
		                    	 */
		                    if($formvalid) {
		                    	print_r("All FIELDS VALIDATED\n");
                                // Regex pattern from form to write
	                            $file_loc = './public/data/appRegex/';
	                            /*
	                             * template file
	                             */
                                $myFile = $file_loc . "testFile.txt";
                                $fh = fopen($myFile, 'w') or die("can't open file");
//                                 $stringData = "Floppy Jalopy2\n";
//                                 fwrite($fh, $stringData);
//                                 $stringData = "Pointy Pinto2\n";
//                                 fwrite($fh, $stringData);
                                $searchTerms = $data['searchTerm'];
//                                 print_r($searchTerms);
                                foreach( $searchTerms as $app ) {
                                			$org_pattern = $app[regexPattern];
                                			/*
                                			 * ',' replaced by '|' for python regex search
                                			 */
                                			$conv_pattern = preg_replace("/,/", "|", $org_pattern);
                                            $stringData = $app[appName] . ": " . $conv_pattern . "\n";
//                                             $stringData = $app[appName] . ": " . $app[regexPattern] . "\n";
//                                             print_r($stringData);
                                            fwrite($fh, $stringData);
                                }
                                fclose($fh);
                                    
                                    /*
                                     * PHP python system call:
                                     */
                                	$exlFile = $data['uploadExl']['tmp_name'];
							        $appProc = 'python ./public/python/appPattern.py ' . $myFile . " ". $exlFile;
							//         $appProc = 'python ./public/python/appPattern.py python/convertedXL.txt ./public/python/schemaPLM.pyc ./public/python/xlsData_PLM.pyc';
							//         exec($appProc, $output, $return);
							//         echo "Dir returned $return, and output: $output\n";
							        $exe_status = system($appProc, $return);
							        echo "Dir returned $return, and output:\n";
							        print_r("exe_status\n");
							        print_r($exe_status);
							        /*
							         * PHP system call non-zero exit status: command failed to execute
							         */
							        if($return){
							        	print_r("python execution failed\n");
							        }

        //                             return $this->redirect()->toRoute('album', array(
        //                                             'controller' => 'Album\Controller\Album',
        //                                             'action'     => 'python',
        //                                 ));
        
							        print_r($exlFile);
							        $exldata = new XlsData($exlFile);
// 							        $exldata->read_rows();
							        $row_arr = $exldata->getRowArr();
// 							        print_r($row_arr);
		                    		print_r("\nNew spreadsheet is created!!!\n");
		                    	}
		                    	else{
		                    		print_r("Search Table input error\n");
		                    	}
		                    
	                    }
                    }
                    else{
                    	$inputfilter->setData($postData);
                    	$err_data = $inputfilter->getValidInput();
                    	print_r($err_data);
                    	
                    }
//                     print_r($data);
            }
    
//             $viewModel = new ViewModel();
//             $viewModel->setTerminal(true);
//             return new ViewModel();
//             return array('form' => $form);
            return array(
                            'form' => $form,
                            'formsub' => $formsub
            );
    }
    
    
    

    public function downloadAction() {
	    $response = $this->getResponse();
// 	    $response->setHeaders(Headers::fromString("Content-Type: application/octet-stream\r\nContent-Length: 9\r\nContent-Disposition: attachment; filename=\"blamoo.txt\""));
// 	    $response->setContent('blablabla');

// 	    /Applications/MAMP/htdocs/myapp/public/python/

	    $xlsx_file_name = "./public/python/appBreakdown.xls";
	    if(file_exists($filename)) {
	    	print_r("appBreakdown.xls exists");
	    }

	    $response->getHeaders()->addHeaders(array(
// 	    		'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	    		'Content-Type' => 'application/vnd.ms-excel',
	    		'Content-Disposition' => 'attachment;filename="appBreak.xls"',
// 	    		'Cache-Control' => 'max-age=0',
	    ));
	    $response->setContent(file_get_contents($xlsx_file_name));

	    return $response;
    }
    
    public function download_sampleAction()
    {
            /* get here all the data you need from the database
             * $size = size of the file you can get by readfile()
            * $filename = 12f3f1aa1b11ec11dd1dd1.zip (with the path)
            * $filename1 = example.zip
            */
    
            if(file_exists($filename)) {
            ob_start();
            $size = readfile($filename);
            $this->view->data = ob_get_clean();
    }
    
      header('Expires: Mon, 20 May 1974 23:58:00 GMT');
          header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
          header('Cache-Control: no-store, no-cache, must-revalidate');
          header('Cache-Control: post-check=0, pre-check=0', false);
          header('Cache-Control: private');
      header('Pragma: no-cache');
      header("Content-Transfer-Encoding: binary");
          header("Content-type: binary/octet-stream");
          header("Content-length: {$size}");
      header("Content-disposition: attachment; filename=\"{$filename1}\"");
    
      $this->_helper->layout()->disableLayout();
      $this->render('download');
    }
    
//     make a view file (download.phtml) with this:
//     <?php echo $this->data; 

    public function pythonAction()
    {
//          return new ViewModel(array(
//             'albums' => $this->getAlbumTable()->fetchAll(),
//         ));
//         $appProc = 'python /Users/Eric/Documents/workspace/TCforTestLink/TestLink/appPattern.py';
//         $appProc = 'python /Applications/MAMP/htdocs/myapp/public/python/appPattern.py';
        $appProc = 'python ./public/python/appPattern.py';
//         $appProc = 'python ./public/python/appPattern.py python/convertedXL.txt ./public/python/schemaPLM.pyc ./public/python/xlsData_PLM.pyc';
//         exec($appProc, $output, $return);
//         echo "Dir returned $return, and output: $output\n";
        system($appProc, $return);
        echo "Dir returned $return, and output:\n";
        //Zend_Debug::dump($output, $label = null, $echo = true);
        //echo "$return \n";
        //echo "============ var_dump ===================\n";
        //var_dump($output);
    }

    // Add content to this method:
    public function addAction()
    {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Album();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                $this->getAlbumTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }
        return array('form' => $form);
    }        
        
        
// Add content to this method:
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'add'
            ));
        }
        $album = $this->getAlbumTable()->getAlbum($id);

        $form  = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getAlbumTable()->saveAlbum($form->getData());

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }        


    // Add content to the following method:
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deleteAlbum($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }

        return array(
            'id'    => $id,
            'album' => $this->getAlbumTable()->getAlbum($id)
        );
    }
        
        
        
        
}