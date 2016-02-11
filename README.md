REST API via Symfony
======

Works with /data directory in server root.

Functions:
------


* ### Create file


##### Description 
Create file from request content

##### URL Structure
```
<root>/files/<fileName>
```
**fileName** The name of your file

##### Method
POST

##### Returns
_201_ File created

##### Errors
_409_ The file already exist or writing error


* ### Update file


##### Description
Update file with request content

##### URL Structure
```
<root>/files/<fileName>
```
**fileName** The name of your file

##### Parameter
**Append** Allows appending to file (true/false)

##### Method
PUT

##### Returns
_200_ File updated

##### Errors
* _404_ The file wasn't found
* _409_ Writing error


* ### Get file


##### Description
Downloads a file.

##### URL Structure
```
<root>/files/<fileName>
```
**fileName** The name of your file

##### Method
GET

##### Returns
The specified file's contents

##### Errors
404 The file wasn't found


* ### Get file metadata


##### Description 
Return file's metadata

##### URL Structure
```
<root>/files/<fileName>/meta
```
**fileName** The name of your file

##### Method
GET

##### Returns
The JSON metadata for the file by the given <fileName>
**Return value definitions**
* _name_ - the file name
* _extension_ - the file extension
* _size_ - the file size in bytes
* _type_ - the file type

##### Errors
_404_ The file wasn't found


* ### Get list of files


##### Description
Return list of files in _/data_ directory

##### URL Structure
```
<root>/files
```

##### Method
GET

##### Returns
The JSON list of files in _/data_ directory


Additional features, that should be, but don't
------

* Storage files in compressed form through ZipArchive.
* Using validator for file limitation: max size, type, etc. Probably via VichUploaderBundle or IphpFileStoreBundle.
* Using https instead of http and authorizing mechanism. Realization via configuration security.yml.
* Access restriction to the file with adding additional information about file owner in file-model.