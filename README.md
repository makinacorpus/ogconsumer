# Open Graph Consumer

Provide a set of helpers for retrieving and managing Open Graph data from
various places over the web.

This API exists because most existing PHP consumers don't provide any high
level API for managing data types, formatting and validating.

## Status

Unfinished! Right now properties cannot be parsed as multiple, the parser
needs to be rewritten from scratch.

## Basic usage

Very basic usage:

    use OgConsumer\Service;

    $service = new Service();

    try {
        $node = $service->fetch(
                "https://www.youtube.com/watch?v=LH5ay10RTGY");
    } catch (\Exception $e) {
        // Your error handling
    }

The $node object is now a valid OgConsumer\Node instance. If the *og:type*
property is registered and known, it can be a specific object instance. In this
case, the *video* type is handled by our core API:

    echo get_class($node); // Outputs "OgConsumer\Object\Video"

For optimal performances you can do multiple HTTP calls at once, using the cURL
parallel HTTP implementation:

    use OgConsumer\Service;

    $service = new Service();

    $nodes = $service->fetchAll(array(
        "https://www.youtube.com/watch?v=upZuJcnQTAw",
        "https://www.youtube.com/watch?v=rTnNwLaTGFI",
        "http://www.nytimes.com/",
        "http://www.nytimes.com/2013/07/29/us/detroit-looks-to-health-law-to-ease-costs.html?hp",
        "http://9gag.com/",
        "http://9gag.com/gag/aOqmnzE",
    ));

    foreach ($nodes as $node) {
        if (false !== $node) {
            echo $node->getTitle(), "\n";
        } else {
            // Your error handling here
        }
    }

Note that using the multiple variation of the API you will need to handle
errors by yourself.

## Advanced usage

### What if I am using Symfony 2 or Zend Framework 2?

Just register an instance of \OgConsumer\Service into your dependency injection
container, and you're ready to go!

### Registering new types

If some types you need to use are not recognized, you can create your own class
to manage them:

    namespace My\Namespace;

    class SomeType extends \OgConsumer\Node
    {
        // Your stuff here
    }

Then in your application bootstrap add:

    \OgConsummer\Service::register(array(
        'some_type' => '\My\Namespace\SomeType',
    ));

Note that you can override existing types the exact same way.

## Additional notes

### HTTP performance

Default cURL implementation of the *FetcherInterface* will attempt to use
*curl_multi_exec()* whenever multiple nodes are fetched altogether. This
gives the best testing results.

From the *RealLifeTest* class fetching 6 pages we had those average numbers:

 + Using *FallbackFetcher*: 21 seconds
 + Using a custom cURL implement (without multi): 8 seconds
 + Using *CurlFetcher*: 3 seconds
