<?php
$lang->zahost->id             = 'ID';
$lang->zahost->common         = 'ZAhost';
$lang->zahost->browse         = 'Host List';
$lang->zahost->create         = 'Add Host';
$lang->zahost->view           = 'Host View';
$lang->zahost->init           = 'Init Host';
$lang->zahost->edit           = 'Edit';
$lang->zahost->editAction     = 'Edit Host';
$lang->zahost->delete         = 'Delete';
$lang->zahost->cancel         = "Cancel";
$lang->zahost->deleteAction   = 'Delete Host';
$lang->zahost->byQuery        = 'Search';
$lang->zahost->all            = 'All';
$lang->zahost->browseNode     = 'ZAnode Browse';
$lang->zahost->deleted        = "Deleted";
$lang->zahost->copy           = 'Click to copy';
$lang->zahost->copied         = 'Copy successful';

$lang->zahost->name        = 'Name';
$lang->zahost->IP          = 'Extranet Address';
$lang->zahost->extranet    = 'Extranet Address';
$lang->zahost->memory      = 'Memory Size';
$lang->zahost->cpuCores    = 'CPU';
$lang->zahost->diskSize    = 'Disk Size';
$lang->zahost->desc        = 'Description';
$lang->zahost->type        = 'Type';
$lang->zahost->status      = 'Status';

$lang->zahost->createdBy    = 'CreatedBy';
$lang->zahost->createdDate  = 'CreatedDate';
$lang->zahost->editedBy     = 'EditedBy';
$lang->zahost->editedDate   = 'EditedDate';
$lang->zahost->registerDate = 'RegisterDate';

$lang->zahost->memorySize   = $lang->zahost->memory;
$lang->zahost->cpuCoreNum   = $lang->zahost->cpuCores;
$lang->zahost->os           = 'System';
$lang->zahost->imageName    = 'Image File';
$lang->zahost->browseImage  = 'Image List';

$lang->zahost->createZanode        = 'Create Zanode';
$lang->zahost->initNotice          = 'Save successfully, initialize the ZAhost or return list';
$lang->zahost->createZanodeNotice  = 'Initialization successful, ready to create the zanode';
$lang->zahost->downloadImageNotice = 'Initialization successful, download the image to create zanode';
$lang->zahost->undeletedNotice     = "The host has nodes that cannot be deleted.";
$lang->zahost->uninitNotice        = 'Please init the host first';
$lang->zahost->netError            = 'Unable to connect to the host, please check the network and try again.';

$lang->zahost->init = new stdclass;
$lang->zahost->init->statusTitle = "Status";
$lang->zahost->init->checkStatus   = "Check Service Status";
$lang->zahost->init->not_install   = "Not installed";
$lang->zahost->init->not_available = "Installed, Not Started";
$lang->zahost->init->ready         = "Ready";
$lang->zahost->init->next          = "Next";

$lang->zahost->init->initFailNotice    = "Fail，Execute the installation service command on the host or <a href='https://github.com/easysoft/zenagent/' target='_blank'>See Help</a>.";
$lang->zahost->init->initSuccessNotice = "Succeed，%s and %s。";

$lang->zahost->init->serviceStatus = [
    "kvm"        => 'not_install',
    "nginx"      => 'not_install',
    "novnc"      => 'not_install',
    "websockify" => 'not_install',
];
$lang->zahost->init->title          = "Initialize Host";
$lang->zahost->init->descTitle      = "Follow these steps to complete the initialization on the host:";
$lang->zahost->init->initDesc       = "Execute the init script on the host: %s %s  <br>- Click check service status button.";
$lang->zahost->init->statusTitle    = "Service Status";

$lang->zahost->image = new stdclass;
$lang->zahost->image->browseImage   = 'Image List';
$lang->zahost->image->createImage   = 'Create Image';
$lang->zahost->image->choseImage    = 'Select Image';
$lang->zahost->image->downloadImage = 'Download Image';
$lang->zahost->image->startDowload  = 'Start Download';

$lang->zahost->image->common     = 'Image';
$lang->zahost->image->name       = 'Name';
$lang->zahost->image->desc       = 'Description';
$lang->zahost->image->path       = 'Image Path';
$lang->zahost->image->memory     = $lang->zahost->memory;
$lang->zahost->image->disk       = $lang->zahost->diskSize;
$lang->zahost->image->os         = $lang->zahost->os;
$lang->zahost->image->imageName  = $lang->zahost->imageName;
$lang->zahost->image->progress   = 'Download Progress';

$lang->zahost->image->statusList['notDownloaded'] = 'Not Downloaded';
$lang->zahost->image->statusList['created']       = 'Inprogress';
$lang->zahost->image->statusList['canceled']      = 'Not Downloaded';
$lang->zahost->image->statusList['inprogress']    = 'Inprogress';
$lang->zahost->image->statusList['completed']     = 'Completed';
$lang->zahost->image->statusList['failed']        = 'Failed';

$lang->zahost->image->imageEmpty            = 'No Image';
$lang->zahost->image->downloadImageFail     = 'Failed to create image task';
$lang->zahost->image->downloadImageSuccess  = 'Successed to create image task';
$lang->zahost->image->cancelDownloadFail    = 'Failed to cancel image task';
$lang->zahost->image->cancelDownloadSuccess = 'Successed to cancel image task';

$lang->zahost->empty         = 'No Host';

$lang->zahost->statusList['wait']    = 'Wait';
$lang->zahost->statusList['ready']   = 'Online';
$lang->zahost->statusList['online']  = 'Online';
$lang->zahost->statusList['offline'] = 'Offline';
$lang->zahost->statusList['busy']    = 'Busy';

$lang->zahost->vsoft = 'VM Software';
$lang->zahost->softwareList['kvm'] = 'KVM';

$lang->zahost->unitList['GB'] = 'GB';
$lang->zahost->unitList['TB'] = 'TB';

$lang->zahost->cpuUnit = 'CORE';

$lang->zahost->zaHostType                 = 'Type';
$lang->zahost->zaHostTypeList['physical'] = 'Physical';

$lang->zahost->confirmDelete           = 'Do you want to delete this host?';
$lang->zahost->cancelDelete            = 'Do you want to cancel this download task?';

$lang->zahost->notice = new stdclass();
$lang->zahost->notice->ip              = '『%s』incorrect format!';
$lang->zahost->notice->registerCommand = 'Register command: ./zagent-host -t host -s http://%s:%s -i %s -p 8086 -secret %s';
$lang->zahost->notice->loading         = 'loading...';
$lang->zahost->notice->noImage         = 'No available image';

$lang->zahost->tips = 'Host include physical host, k8s clusters, cloud servers, and cloud container instances. Host used to create VMS or container instances. The recommended host OS is ubuntu or CentOS LTS.';
