# Open Graph Consumer

Provide a set of helpers for retrieving and managing Open Graph data from
various places over the web.

This API exists because most existing PHP consumers don't provide any high
level API for managing data types, formatting and validating.

## Usage

### Simple example

    use OgConsumer\Service;

    $service = new Service();

    try {
        $node = $service->fetch("https://www.youtube.com/watch?v=LH5ay10RTGY");

        foreach ($node->getImageAll() as $iamge) {
            echo "<img src=\"", $image->getUrl(), "\"/>\n";
        }
        if ($video = $node->getVideo()) {
            // Of course this one is fake don't copy/paste it!
            echo "<object" src=\"", $video->getUrl(), "\"/>\n";
        }

        // ...

    } catch (\Exception $e) {
        // Your error handling
    }

### Array handling

Official specification is not really clear about how we should handle duplicate
properties in the parsed data, most examples don't have but some have, see
*http://ogp.me/#array* for more information.

This API handle every value or structure object property as a single value
internally until the parser finds duplicates: every property may arbitrary be
converted to an array in the node object graph.

For example, consider this example (from *http://ogp.me/#array*):

        <meta property="og:image" content="http://example.com/rock.jpg" />
        <meta property="og:image:width" content="300" />
        <meta property="og:image:height" content="300" />
        <meta property="og:image" content="http://example.com/rock2.jpg" />
        <meta property="og:image" content="http://example.com/rock3.jpg" />
        <meta property="og:image:height" content="1000" />

The API will convert this as a PHP array of OgConsumer\Object\Image instances
you can manipulate quite easily:

    $node = $service->fetch('file://resources/image-array.html');

    // Fetch the first image
    $image = $node->getImage();
    echo "Image is ", $image->getUrl(), "\n";

    // Fetch all images
    foreach ($node->getAllImages() as $index => $image) {
        echo "Image #", $index, " is ", $image->getUrl(), "\n";
    }

This API is liberal in what it accepts and does not include node parsing for
the complete standard nor does schema based introspection, if you need to
access arbitrary parsed data without extending the API you can use those
two generic methods:

    // Get the first "some_key" property parsed
    $node->get('some_key');

    // Get all the "some_key" properties parsed
    $node->getAll('some_key');

### Fetching multiple nodes at once

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

The package provides basic handling for those media types:

 *  Audio
 *  Image
 *  Video

If you need to extend the parser in order to be able to fetch other structured
objects, you need to register their namespace:

    namespace My\Namespace;

    class SomeType extends \OgConsumer\Object
    {
        // Your stuff here
    }

Then in your application bootstrap add:

    \OgConsummer\Type::register(array(
        'some_type' => '\My\Namespace\SomeType',
    ));

Note that you can override existing types.

If you don't want to write your own class, you can also set the array values
as an explicit null, and you new structured object type will use the default
*\OgConsumer\Object* class:

    \OgConsummer\Type::register(array(
        'foo' => null,
        'bar' => null,
        'baz' => '\Foo\Bar',
    ));

## Additional notes

### HTTP performance

Default cURL implementation of the *FetcherInterface* will attempt to use
*curl_multi_exec()* whenever multiple nodes are fetched altogether. This
gives the best testing results.

From the *RealLifeTest* class fetching 6 pages we had those average numbers:

 + Using *FallbackFetcher*: 21 seconds
 + Using a custom cURL implement (without multi): 8 seconds
 + Using *CurlFetcher*: 3 seconds
