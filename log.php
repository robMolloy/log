<?php

function showAllLogEntries(){
    $contents = trim(file_get_contents('/var/log/apache2/error.log'));
    //~ $contents = file_get_contents('/var/log/apache2/error.log');
    $array = explode("\n",$contents);
    //~ return array_filter($array);
    $r_array = array_reverse($array);
    return $array==[''] ? [] : $array;
}

function trigger_notice($notice){
    $type = gettype($notice);
    $noticeString = ($type=='string' ? $notice : json_encode($notice));
    $noticeString = str_replace(['<',   ','],['&lt', ', '],$noticeString);
    
    trigger_error('<div>'.strtoupper($type).': <br>'.$noticeString.'</div>');
}

$nav = (isset($_REQUEST['nav']) ? $_REQUEST['nav'] : '');
trigger_notice('gfy');
switch($nav){
    case 'clearLog':
        exec('echo "">/var/log/apache2/error.log');
        echo True;
    break;
    
    case 'showAllLogEntries':
        echo json_encode(showAllLogEntries());
    break;
    
    case 'restartApache':
        exec('sudo /etc/init.d/apache2 restart');
    break;
    
    case '':
?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="height=device-height width=device-width initial-scale=1">
    <style type="text/css">
    *       {margin:0;padding:0;color:#222222;box-sizing:border-box;}
    
    body    {background-color:#EEEEEE;font-family:'Montserrat';max-height:100vh;}
    main    {text-align:center;margin:10px auto 10px auto;}
    a       {text-decoration:none;}
    
    .wrapperMain {
        min-width:45%;max-width:1100px;text-align:center;overflow-wrap:break-word;
        /*.singleColumn*/
        display:inline-grid;grid-template-columns:repeat(1,auto);grid-row-gap:20px;
    }
     
    input[type=text], input[type=password], textarea {
        padding:7px;background-color:#EEEEEE;color:#222222;
        border:1px solid #CCCCCC;width:100%;
    }
    input[type=text]:focus, input[type=password]:focus, textarea:focus {background-color:#FFFFFF;}
    textarea {resize:vertical;height:100px;}

    button {
        background-color:#222222;border:1px solid #CCCCCC;border-radius:3px;
        color:#FFFFFF;padding:5px 10px;text-align: center;text-decoration: none;
        display:inline-block;text-transform:uppercase;font-size:16px;cursor:pointer;
    }
    
    button:hover {background-color:#EEEEEE;color:#CCCCCC;}
    
    .button {
	    background-color:#EEEEEE;
	    color:#FFFFFF;
	    padding:5px;
	    font-size:30px;
	    font-weight:900;
	    border-radius:3px;
	    cursor:pointer;
	}
    .button * {
	    color:#FFFFFF;
	}
	
	.button:hover {
	    background-color:#CCCCCC;
	}
    
    .hidden {display:none !important;}
    .error {font-size:15px;color:#FF0000;}

    .singleColumn {display:grid;grid-template-columns:repeat(1,auto);grid-row-gap:2px;}
    .oneLineContents {display:flex;align-items:center;}
    .centerContents {display:flex;justify-content:center;align-items:center;}
    .centerContentsHorizontally {display:flex;justify-content:center;align-items:flex-start;}
    .centerContentsVertically {display:flex;align-items:center;}
    
    .panel {
        background-color:#FFFFFF;padding:20px;
        /*.singleColumn*/
        display:grid;grid-template-columns:repeat(1,auto);grid-row-gap:5px;
    }
    
    .list{
        border-top: solid 3px #EEEEEE;border-bottom: solid 3px #EEEEEE;background-color:#EEEEEE;max-height:83vh;overflow-y:auto;
        /*.singleColumn*/
        display:grid;grid-template-columns:repeat(1,auto);grid-row-gap:2px;
    }
    .list > * {
        background-color:#FFFFFF;padding:10px;
        /*.singleColumn*/
        display:grid;grid-template-columns:repeat(1,auto);grid-row-gap:2px;
    }
    .list > *:hover {background-color:#EEEEEE;}
    .list > *.select {background-color:#CCCCCC;}
    .list > * > * {display:block;margin:5px 20px;text-align:left;letter-spacing:1px;font-style:oblique;}
    
    
    .textBlock {white-space:pre-wrap;}
    .buttonBar {text-align:center;}
    
    .displayMode {text-align:left;}
    .editMode {text-align:left;}
    
    .titleBar {display:flex;}
    .titleBar > * {display:flex;align-items:center;justify-content:center;flex:1}
    .titleBar > div {flex:9;display:grid;grid-template-columns:repeat(1,auto);grid-row-gap:3px;line-height:1;}
    .titleBar > div > h1 {cursor:pointer;}
    
    #responseLogIcon {background-image:url("img/icon.png");background-size:cover;position:fixed;bottom:20px;right:20px;min-height:50px;min-width:50px;cursor:pointer;z-index:1;}
    #responseLog {background-color:#FFCCCC;position:fixed;display:inline-block;overflow-wrap:break-word;bottom:20px;right:20px;height:40vh;width:40vw;min-width:250px;border-radius:0 0 30px 0;overflow-y:auto;}
    
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .sliderContainer {display:flex;justify-content:center;align-items:center;}
    .sliderContainer > * {padding:0 10px;}


    .slider {border-radius: 34px;position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#CCCCCC;}
    .slider:before {border-radius: 50%;content:""; position:absolute; height:26px; width:26px; left:4px; bottom:4px; background-color: white;}


    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }
    
    @media(max-width:768px){
        .wrapperMain {min-width:100vw;max-width:100vw;}
    }

    </style>
    
    <script type="text/javascript">
        let hideLogEntries = [];
        let allLogEntries = [];
        let selectedEntries = [];
        
        function ajax(params={}) {
            let file = ('file' in params ? params.file : ''); //~ !essential parameter!
            let f = ('f' in params ? params.f : new FormData());
            let nav = ('nav' in params ? params.nav : ''); //~ !pass in file or essential parameter!
            
            if(nav!=''){f.append('nav',nav);}
            
            return new Promise((resolve, reject) => {
                const request = new XMLHttpRequest();
                request.open("POST", file);
                request.onload = (()=>{
                    if (request.status == 200){
                        resolve(request.response);
                    } 
                    else {reject(Error(request.statusText));}
                });
                request.onerror = (()=>{reject(Error("Network Error"));});
                request.send(f);
            });
        }
        
        
        function getAllSelected(){
            return document.getElementById('list').querySelectorAll('.select');
        }
        
        function initElement(element=''){
            return element.nodeName==undefined ? document.getElementById(element) : element;
        }
        
        function updateXButton(){
            let gASlength = getAllSelected().length;
            
            document.getElementById('xButton').innerHTML = `<span>X${gASlength==0 ? `` : `<sub>${gASlength}</sub>`}</span>`;
        }
        
        
        async function hideSelected(){
            let selected = getAllSelected();
            if(selected.length>0){
                selected.forEach((value,key)=>{
                    hideLogEntries.push(getIdFromLogEntryElement(value));
                    value.remove();
                    selectedEntries = [];
                });
            } else {
                await clearLog();
                selectedEntries = [];
            }
            updateXButton();
        }
        
        function getIdFromLogEntryElement(elm){
            return Number(elm.id.split('_')[1]);
        }
        
        async function clearLog(){
            let response = await ajax({'file':'?nav=clearLog'});
            hideLogEntries = [];
            allLogEntries = [];
            selectedEntries = [];
            showAllLogEntries();
            
            return response;
        }
        
        function ifCheckedRefresh(){
            if(document.querySelector('input[type="checkbox"]').checked){
                showAllLogEntries();
            }
        }
        
        async function showAllLogEntries(descending=true){
            let list = document.getElementById('list');
            let response = await ajax({'file':'?nav=showAllLogEntries'});
            let json = JSON.parse(response);
            
            allLogEntries = Object.keys(json);
            
            list.innerHTML='';
            position = descending ? 'afterbegin' : 'beforeend';
            json.forEach((value,key)=>{
                if(!hideLogEntries.includes(key)){
                    list.insertAdjacentHTML(position,`<div id="logEntry_${key}" onclick="toggleSelect(this);" ${selectedEntries.includes(key) ? `class="select"` : ``}>${value}</div>`);
                }   
            });
        }
        
        async function restartApache(){
            let rButton = document.getElementById('rButton')
            
            rButton.innerHTML = '';
            setTimeout(function(){rButton.innerHTML = '.';},1000);
            setTimeout(function(){rButton.innerHTML = '..';},2000);
            setTimeout(function(){rButton.innerHTML = '...';},3000);
            setTimeout(function(){rButton.innerHTML = 'R';},3000);
            
            await ajax({'file':'?nav=restartApache'})
        }
        

        function toggleSelect(elm){
            if(elm.classList.contains('select')){
                unselectEntry(elm);
            } else {
                selectEntry(elm);
            }
        }
        
        function selectAll(){
            let currentLogEntries = getAllVisibleEntries();
            currentLogEntries.forEach((value)=>selectEntry(value));
        }
        
        function unselectAll(){
            let currentLogEntries = getAllVisibleEntries();
            currentLogEntries.forEach((value)=>unselectEntry(value));
        }
        
        function selectEntry(elm){
            elm.classList = 'select';
            selectedEntries.push(getIdFromLogEntryElement(elm));
            updateXButton();
        }
        
        function unselectEntry(elm){
            elm.classList = '';
            let index = selectedEntries.indexOf(elm);
            selectedEntries.splice(index, 1);
            updateXButton();
        }
        
        function getAllVisibleEntries(){
            return Array.from(document.getElementById('list').children);
        }
        
        function allEntriesAreSelected(){
            let currentLogEntries = getAllVisibleEntries();
            let allSelected = true;
            currentLogEntries.forEach((value)=>{
                if(!value.classList.contains('select')){allSelected = false;}
            });
            return allSelected;
        }
        
        function toggleSelectAll(){
            if(allEntriesAreSelected()){
                unselectAll();
            } else {
                selectAll();
            }
        }

        
    </script>
</head>

<body onload="showAllLogEntries();setInterval(function(){ifCheckedRefresh()}, 10000);">
    <main>
        <div id="log"></div>
        <div class="wrapperMain" id="wrapperMain">
            <div class="panel">
                <div class="titleBar">
                    <span id="rButton" class="button" onclick="restartApache();">R</span>
                    <div>
                        <h1 onclick="toggleSelectAll()">Error log</h1>
                        <div class="sliderContainer" style="">
                            <h2>O</h2>
                            <label class="switch"><input type="checkbox" checked="checked"><span class="slider"></span></label>
                            <h2>I</h2>
                        </div>
                    </div>
                    <span id="xButton" class="button" onclick="hideSelected()"><span>X</span></span>
                </div>
                <div id="list" class="list">
                </div>
            </div>
        </div>
    </main>
</body>
<?php
    break;
}
?>
