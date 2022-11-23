<?php 
    function rrmdir($dir) {
        if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
            if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
        }
    }

    $base_url = 'http://localhost/php_uzdt1/';
    $dirname = "1";

    if(isset($_GET['delete']) && $_GET['delete'] != './index.php') {
        if(is_dir($_GET['delete'])) {
            rrmdir($_GET['delete']);
        } else {
            unlink($_GET['delete']);
        }
        //header('Location: ' . $base_url);
        header('Location:' . $base_url . (isset($_GET['path']) ? '?path=./' . $_GET['path'] : ''));
        exit();
    }

    

    if(isset($_GET['action']) && $_GET['action'] === 'new_dir') {
        mkdir( (isset($_GET['path']) ? $_GET['path'] : '') . $_POST['directory_name']);
        header('Location: ' . $base_url . (isset($_GET['path']) ? '?path=./' . $_GET['path'] : '') );
        exit();
    }

    // if(isset($_GET['delete'])) {
    //     echo $_GET['delete'];
    //     rmdir($_GET['delete']);
    //     //rmdir((isset($_GET['path']) ? $_GET['path'] : '') );
    //     //header('Location: ' . $base_url);
    //     //exit();
    // }

    if(isset($_GET['action']) && $_GET['action'] === 'new_file') {
        file_put_contents( (isset($_GET['path']) ? $_GET['path'] : '') . $_POST['file_name'] , $_POST['file_content']);
        header('Location:' . $base_url . (isset($_GET['path']) ? '?path=./' . $_GET['path'] : ''));
        exit();
    }

    if(isset($_FILES['file'])){
        $errors= array();
        $file_size =$_FILES['file']['size'];
        $file_tmp =$_FILES['file']['tmp_name'];
        $file_type=$_FILES['file']['type'];
        $filetype_array = explode('.', $_FILES['file']['name']);
        $file_ext= strtolower(end($filetype_array));
        $file_name = time() . '.' . $file_ext; 
            
        $extensions= array('jpg', 'png', 'txt', 'pdf');
            
        if(in_array($file_ext,$extensions)=== false){
            $errors[]="extension not allowed, please choose a valid file.";
        }
            
        if($file_size > 2097152){
            $errors[]='File size must be excately 2 MB';
        }
            
        if(empty($errors)==true){
            move_uploaded_file($file_tmp, $_GET['path'] . $file_name);
            echo "Success";
        }else{
             print_r($errors);
        }
         
    }
        
?>

<!DOCTYPE html>
<html>
    <head>
        <title>File System Browser</title>
        <!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <style>
            * {
                font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            table td, table th {
                border: 1px solid #ddd;
                padding: 8px;
            }
            table tr:nth-child(even){
                background-color: #f2f2f2;
            }
            table tr:hover{
                background-color: #ddd;
            }
            table th {
                padding-top: 12px;
                padding-bottom: 12px;
                text-align: left;
                background-color: #084298;
                color: white;
            }

            .back-btn {
                background: blue;
                border: none;
                color: white;
            }

            .possition1 {
                margin-top: 10px;
                margin-bottom: 10px;
                /* margin-left: 10px; */
                background-color: #084298;
                color: white;
                
            }
            .padding {
                padding: 10px;
            }

            .spacing {
                padding-bottom: 10px;
            }
        </style>
    </head>
    <body>
        <div class="padding">
        <button class="possition1" onclick="history.go(-1);" >Back </button>
        <form method="POST" action="<?= $base_url . '?action=new_dir' . (isset($_GET['path']) ? '&path=./' . $_GET['path'] : '') ?>">
            <div class="spacing">
                <input  type="text" name="directory_name" />
                <button> Create new folder</button>
            </div>
        </form>
        <form method="POST" action="<?= $base_url . '?action=new_file' . (isset($_GET['path']) ? '&path=./' . $_GET['path'] : '') ?>"> 
            <div class="spacing">
                <input  type="text" name="file_name" />
                <button >Create new file</button>
            </div>
            <div class="spacing"><textarea  name="file_content"></textarea></div>
            
        </form>
        <form action="<?= $base_url . (isset($_GET['path']) ? '?path=./' . $_GET['path'] : '') ?>" method="POST" enctype="multipart/form-data">
            <input type="file" name="file" />
            <input type="submit"/>
        </form>
        </div>
        <?php 
            $path = isset($_GET["path"]) ? './' . $_GET["path"] : './' ;
            $files_and_dirs = scandir($path);
            

            print('<h2>Directory contents: ' . str_replace('?path=','',$_SERVER['REQUEST_URI']) . '</h2>' );
            
            // List all files and directories
            print('<table><th>Type</th><th>Name</th><th>Actions</th>');
            foreach ($files_and_dirs as $fnd){
                if ($fnd != ".." and $fnd != ".") {
                    print('<tr>');
                    print('<td>' . (is_dir($path . $fnd) ? "Directory" : "File") . '</td>');
                    print('<td>' . (is_dir($path . $fnd) 
                                ? '<a href="' . $base_url . (isset($_GET['path']) 
                                        ? '?path='. $_GET['path']. $fnd . '/' 
                                        : '?path=' . $fnd . '/') . '">' . $fnd . '</a>'
                                : $fnd) 
                        . '</td>');
                    print('<td>
                            <a href="' .$base_url. '?delete='.$path.$fnd. (isset($_GET['path']) ? '&path=' . $_GET['path'] : '') .'">Delete</a>
                        </td>');
                    print('</tr>');
                }
            }
            print("</table>");
        
        ?>
    </body>
</html>