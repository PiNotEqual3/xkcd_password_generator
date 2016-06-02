<html>
<head>
    <title>xkcd Passwort Generator</title>
    <script type="application/javascript" src="sha1-min.js"></script>
    <script type="application/javascript" src="words.js"></script>
    <script type="application/javascript">
        // Quelle: http://preshing.com/20110811/xkcd-password-generator/
        var xkcd_pw_gen_server_hash = "<?= sha1(mcrypt_create_iv(100, MCRYPT_DEV_URANDOM)) ?>";

        // Get some entropy from the system clock:
        function xkcd_pw_gen_time_ent()
        {
            var d = 1 * new Date();
            var i = 0;
            while (1 * new Date() == d)
                i++; // Measure iterations until next tick
            return "" + d + i;
        }

        // Return a pseudorandom array of four 32-bit integers:
        function xkcd_pw_gen_create_hash()
        {
            // Entropy string built in a manner inspired by David Finch:
            var entropy = xkcd_pw_gen_server_hash + xkcd_pw_gen_time_ent();
            entropy += navigator.userAgent + Math.random() + Math.random() + screen.width + screen.height;
            if (document.all)
                entropy = entropy + document.body.clientWidth + document.body.clientHeight + document.body.scrollWidth + document.body.scrollHeight;
            else
                entropy = entropy + window.innerWidth + window.innerHeight + window.width + window.height;
            entropy += xkcd_pw_gen_time_ent();

            // Hash and convert to 32-bit integers:
            var hexString = hex_sha1(entropy); // from sha1-min.js
            var result = [];
            for (var i = 0; i < 32; i += 8)
            {
                result.push(parseInt(hexString.substr(i, 8), 16));
            }
            return result;
        }
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        // Generate a new passphrase and update the document:
        function xkcd_pw_gen()
        {
            var hash = xkcd_pw_gen_create_hash();

            // casing
            var uppercase;
            var casing = document.querySelector('input[name="casing"]:checked').value;
            if (casing == 1) { uppercase = false; }
            if (casing == 2) { uppercase = true; }
            if (casing == 4) { uppercase = Math.random() > 0.5; }

            // sperator
            var sperators = document.getElementById('sperators').value;
            var sperator = sperators[Math.round(sperators.length * Math.random())];
            if (sperator == null) sperator = '';

            // Words
            var choices = [];
            var word_count = document.getElementById('word_count').value;
            for (var w = 0; w < word_count; w++)
            {
                var jsRandom = Math.floor(Math.random() * 0x100000000);
                var index = ((jsRandom ^ hash[w]) + 0x100000000) % xkcd_pw_gen_wordlist.length;
                if (uppercase)
                {
                    choices.push(capitalizeFirstLetter(xkcd_pw_gen_wordlist[index]));
                }
                else
                {
                    choices.push(xkcd_pw_gen_wordlist[index]);
                }
            }
            var result = document.getElementById("xkcd_pw_gen_result");
            result.innerHTML = result.textContent = choices.join(sperator);
            result.focus();
            try {
                var range = document.createRange();
                range.selectNodeContents(result);
                window.getSelection().addRange(range);
                document.execCommand('copy')
            } catch (err){}
        }

    </script>
</head>
<body>
    <h1>xkcd Passwort Generator</h1>
    <div id="xkcd_pw_gen_result" style="margin: 10px; padding: 5px; display: inline-block; background-color: lightgray;"></div>
    <div>
        <label>
            Words: <input id="word_count" type="number" value="4">
        </label>
    </div>
    <div>
        Casing:
        <label><input type="radio" name="casing" value="1"> Lower</label>
        <label><input type="radio" name="casing" value="2"> Upper</label>
        <label><input type="radio" name="casing" value="4" checked> Both</label>
    </div>
    <div>
        <label>
            Seperators: <input id="sperators" type="text" value=" ,.-">
        </label>
    </div>
    <p></p>
    <div><button onclick="xkcd_pw_gen()">Neues Passwort</button></div>

    <br><br>
    <img src="https://imgs.xkcd.com/comics/password_strength.png"><br>
    <a href="https://xkcd.com/936/">xkcd #936</a>

    <script type="application/javascript">xkcd_pw_gen();</script>
</body>
</html>