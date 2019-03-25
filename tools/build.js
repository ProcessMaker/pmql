var pegjs = require("pegjs");
var phpegjs = require("phpegjs");
var fs = require("fs");
var argv = require('minimist')(process.argv.slice(2));

// Check for required parameters
if(!argv.i || !argv.o || !argv.n || !argv.c) {
    console.log(`
Usage: build.js -i <input grammar file> -o <output source file> -n <namespace> -c <classname>

Example: node ./buildjs -i query.peg -o src/Parser.php -n ProcessMaker\\Query -c Parser
    `);
    process.exit(-1);
}
// Read in the file specified by argument input
try {
    var grammar = fs.readFileSync(argv.i, 'utf8');
} catch(err) {
    console.log(err.message);
    process.exit(-2);
}

try {
    var parser = pegjs.generate(grammar, {
        plugins: [phpegjs],
        phpegjs: {
            parserNamespace: argv.n,
            parserClassName: argv.c
        }
    });
} catch(err) {
    console.log(err.message);
    process.exit(-5);
}

// Write the output to the file specified by argument output
try {
    fs.writeFileSync(argv.o, parser, 'utf8');
} catch(err) {
    console.log(err.message);
    process.exit(-3);
}
