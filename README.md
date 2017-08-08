# DocumentSerializer
A simple PHP Trait to make Doctrine MongoDB ODM Documents serializable to Array or JSON

This code is based/forked on this [gist](https://gist.github.com/ajaxray/94b27439ba9c3840d420)

Some methods have been adjusted and some errors corrected.
I'll implement a few more things.

Using:

```PHP
<?php
/**
 * @ODM\Document(collection="users")
 */
class User
{
    // Using this trait
    use DocumentSerializer;
    
    
    // ... Your as usual document code
}
```

```PHP
<?php
// Using in Zend Framework 3
// Recovering DocumentManager
$dm = $this->sm->get('doctrine.documentmanager.odm_default')
$user = $dm->getRepository('your\document\repository')->find($id);
// Will return simple PHP array
$docArray = $user->toArray();
// Will return JSON string
$docJSON = $user->toJSON();
```
