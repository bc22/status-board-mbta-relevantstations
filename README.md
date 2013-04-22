
MBTA Relevant Stations for [Panic](http://www.panic.com)&rsquo;s [Status Board](http://www.panic.com/statusboard).
==================================

This is a small Status Board Table widget that shows you the time until the next two trains arrive at your station(s) of choice. This only supports red/green/orange lines for the time being.

<img src="http://f.cl.ly/items/0c2q2F0j3N2w3i3Y3W03/IMG_0002.PNG">

Getting Started
---------------
1. Clone to your local directory with Apache + PHP installed. 
2. Configure *mbta.php* to contain the lines and stops that you care about. 
3. Create a new Table widget and point it to your URL. 
4. ???
5. Profit! 

Features
--------

This is a very simple first pass at throwing some MBTA data on to a dashboard. Right now, it only works for "heavy rail" lines, which is a fancy way of saying that there isn't support for bus/ferry service, Commuter Rail service or the Green Line. 

Basically, you configure one variable in the main *mbta.php* file:

````php
    $data =
        array(
        "red"     => array( "Kendall/MIT", "Charles/MGH" ),
        "orange"  => array( "Downtown Crossing" ),
        "blue"    => array( "Government Center" )
    );
````

And your Status Board will show, for each line and stop, the time you'll have to wait for the next two trains in each direction of service. 

Depending on how things go, I'll add support for whatever we can going forward.

This is my first foray into the world of open source on Github, so be gentle. 


Feedback
-------
Is welcomed! As I said, this is my first time open-sourcing code, so I'd be interested to see folks' reception.

As I have a rewarding day job, I'll update this as often as possible.