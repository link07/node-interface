# Bitcoin Node Interface

This is a basic web status page for Bitcoin (and related) full nodes. If you want to see how it looks, a demo is available [here](https://ltc.xblau.com).

This was originally built as a Litecoin node interface, but it works fine on Bitcoin nodes, and other related nodes.

You can use it to check thing like:

- **Node uptime** (but only on very recent versions of Litecoin Core).
- **Sync status** (by looking at the number of blocks/headers).
- **Network usage:** How much data your node has sent/received (since daemon start).
- **Connected peers:** Info about inbound ant outbound connections to your node.

If you have any problems or suggestions, feel free to open an issue or pull request.

### [Security](#security)

I *think* this is secure, but that doesn't mean there's not a security hole somewhere. If you find one, please contact me.

Before doing anything, consider reviewing the source code (or ask someone you trust to do it) to check that it's not doing anything malicious.

There's not any type of authentication mechanism built in. If you want to restrict access, use the features provided by your web server to do so.

**WARNING:** if your full node has a wallet enabled, this software has access to it. It no longer prevents you to run it if a wallet is found, but please *do not use this* if your node has a non-zero balance.

### [Requirements](#requirements)

To run this you need a web server (like Apache) with a recent version of PHP with curl support. The web server can be installed on a different machine, but you will need to allow RPC calls from it.

On Debian and derivates (Ubuntu, etc) you can install everything with
    
    # apt-get install apache2 libapache2-mod-php php-curl

For other distros, there are a lot of awesome tutorials online you can check.

On Windows, easyphp does this fairly easily.

### [Installing](#installing)

First thing you need to do is add an username/password to the node configuration. Add the following lines to your `litecoin.conf` file:

    rpcuser=someusername
    rpcpassword=somethingsecret

Once you do it, restart the daemon:

    $ bitcoin-cli stop
    $ bitcoind -daemon

After that, clone this repository in an empty, publicly accessible directory:

    $ git clone https://github.com/link07/node-interface.git .

Now, copy (*not rename*) the example config file to `config.php`:

    $ cp config-example.php config.php

Using your favorite editor, edit `config.php` and replace the default username and password with the ones specified on your config file.

After you do that, open your browser and go to the webpage. If your node is running, everything should be working.

### [Updating](#updating)

Simply run `git pull`. This should get any new commits and apply them to your local copy.

### [License](#license)

This project is free software, licensed under the terms of the MIT License. See the `LICENSE` file on this repository or [opensource.org/licenses/mit](https://opensource.org/licenses/mit).

If you liked or found this useful, starring this repo on GitHub or sending an email (even if it's just a "Hey, I'm using your project") helps a lot!

Also, in case you want to send a tip (to this fork):
ltc: `LM2YuS1yfgdXSLmE7pyMYNQ7QqiBrTaoyq`.
btc: `1MgNbJtxbnTXdo95NiHA7kbSdeVR28o65C`

Or to the original creator: `LXBLauKyugeQGY2m8JZSMQJzc9rR1yNXgR`
