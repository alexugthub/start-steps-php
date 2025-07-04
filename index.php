<?php

/**
 * Start Steps
 * A humble beginning for what starts as a note taking web application
 * @link https://alexu.click/projects/steps
 */

//==============================================================================
// CONFIGURATION
//==============================================================================

// Application version
define("VERSION", "0.0.1");

// Debug mode for local testing
define("DEBUG", true);

// Application protected location (upper directory)
define("APP_FOLDER", ".." . DIRECTORY_SEPARATOR);

// SQLite database connection information
define("SQLITE_FILE", "steps.db");

//==============================================================================
// REQUEST
//==============================================================================

// Request method (GET, POST, PUT, DELETE)
$method         = strtolower($_SERVER["REQUEST_METHOD"]);
// Data from all supported requests
$request        = array_merge(
    json_decode(file_get_contents("php://input"), true) ?? [],
    $_GET ?? []
);
// All the headers in the request, in lowercase
$headers        = array_change_key_case(getallheaders(), CASE_LOWER);
// Type of content to return
$type           = $headers["accept"] ?? "text/html";
// Operation requested from the client side
$operation      = $headers["operation"] ?? "";
// Default title
$title          = "Steps";
// Default description
$description    = "A simple note taking application (for now)";

//==============================================================================
// RESPONSE
//==============================================================================

// Data for the JSON response
$response     = [
  // Whether request is considered successful or not
  "success"   => false
];

//==============================================================================
// FUNCTIONS
//==============================================================================

/**
 * Connects to the database and returns the data object
 *
 * @param $config Array with connection information
 */
function database($config = [])
{
    // If parameters not specified, use definitions
    $file = $config["file"] ?? APP_FOLDER . SQLITE_FILE;

    // Create SQLite file if it does not exist
    if (!is_file($file)) {
        file_put_contents($file, null);
    }

    // Give it a try
    try {
        // Create a new connection
        $dbh = new PDO("sqlite:$file");

        // Enable foreign keys
        $dbh->exec("PRAGMA foreign_keys = ON;");

        // Return PDO
        return $dbh;
    } catch (PDOException $e) {
        var_dump($e->getMessage());
        // Return nothing
        return null;
    }
}

/**
 * Installs the application for the first time
 */
function install()
{
    // Connect to the database to create the initial tables
    $dbh = database();
    if ($dbh) {
        // Create the contexts table, for holding different contexts
        $dbh->exec(
            <<<EOD
                CREATE TABLE IF NOT EXISTS "contexts" (
                    "ID"     INTEGER NOT NULL,           -- Unique ID
                    "active" INTEGER NOT NULL DEFAULT 0, -- Currently used
                    PRIMARY KEY("ID" AUTOINCREMENT)
                );

                CREATE UNIQUE INDEX IF NOT EXISTS "ContextID" ON "contexts" (
                    "ID"
                );

                INSERT OR IGNORE INTO "contexts"
                    ("ID", "active")
                    VALUES ('1', '1');
            EOD
        );

        // Create the nodes table, for the hierarchy of nodes
        $dbh->exec(
            <<<EOD
                CREATE TABLE IF NOT EXISTS "nodes" (
                    "ID"	    INTEGER NOT NULL,            -- Unique ID
                    "contextID"	INTEGER NOT NULL,            -- Context
                    "parentID"	INTEGER DEFAULT NULL,        -- Parent node
                    "type"	    TEXT NOT NULL DEFAULT 'text' -- Node type
                                CHECK(
                                    type IN (
                                        'text', 'section', 'list', 'routine'
                                    )
                                ),
                    "position"	INTEGER NOT NULL DEFAULT 0,  -- Position
                    PRIMARY KEY("ID" AUTOINCREMENT),
                    FOREIGN KEY("contextID") 
                        REFERENCES "contexts"("ID")
                            ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY("parentID")
                        REFERENCES "nodes"("ID")
                            ON DELETE CASCADE ON UPDATE CASCADE
                );

                CREATE UNIQUE INDEX IF NOT EXISTS "NodeID" ON "contexts" (
                    "ID"
                );

                INSERT OR IGNORE INTO nodes
                    ("ID", "contextID", "parentID", "type", "position")
                    VALUES ('1', '1', NULL, 'text', '0');

                INSERT OR IGNORE INTO nodes
                    ("ID", "contextID", "parentID", "type", "position")
                    VALUES ('2', '1', NULL, 'text', '0');

                INSERT OR IGNORE INTO nodes
                    ("ID", "contextID", "parentID", "type", "position")
                    VALUES ('3', '1', NULL, 'text', '0');
            EOD
        );

        // Create the texts table, for simple blocks of text
        $dbh->exec(
            <<<EOD
                CREATE TABLE IF NOT EXISTS "texts" (
                    "ID"	  INTEGER NOT NULL,           -- Unique ID
                    "nodeID"  INTEGER NOT NULL,           -- Assigned node
                    "content" TEXT DEFAULT NULL,          -- Full content
                    "lang"    TEXT NOT NULL DEFAULT 'en', -- Language
                    "version" INTEGER NOT NULL DEFAULT 0, -- Changes version
                    FOREIGN KEY("nodeID")
                        REFERENCES "nodes"("ID")
                            ON DELETE CASCADE ON UPDATE CASCADE,
                    PRIMARY KEY("ID" AUTOINCREMENT)
                );

                CREATE UNIQUE INDEX IF NOT EXISTS "TextID" ON "texts" (
                    "nodeID"
                );

                INSERT OR IGNORE INTO "texts"
                    ("ID", "nodeID", "content", "lang", "version")
                    VALUES ('1', '1', 'Title', 'en', '0');
  
                INSERT OR IGNORE INTO "texts"
                    ("ID", "nodeID", "content", "lang", "version")
                    VALUES ('2', '2', 'Description', 'en', '0');

                INSERT OR IGNORE INTO "texts"
                    ("ID", "nodeID", "content", "lang", "version")
                    VALUES ('3', '3', 'Content', 'en', '0');
            EOD
        );

        // Read this same file
        $code = file_get_contents(__FILE__);

        // Remove installer invocation
        $code = str_replace("install();\r\n", "// install();\r\n", $code);
        $code = str_replace(
            "install();" . PHP_EOL,
            "// install();" . PHP_EOL,
            $code
        );

        // To test in debug mode, save as different file
        $path = DEBUG ? "index.debug.php" : "index.php";

        // Save code with new configuration
        file_put_contents($path, $code);
    } else {
        // If database connection failed, return false
        $response["message"] = "Failed to connect to the database";
    }
}

// Install (invocation is commented out once installed)
install();

// Handle request and generate response based on content type
switch ($type) :
    //==========================================================================
    // JSON
    //==========================================================================

    // Client side requests of the APIs operations
    case "application/json":
        // Handle each method and requested operation
        switch ("$method:$operation") {
            // Get a note by its ID
            case "get:note":
                // Validate request data
                if (!is_numeric($request["node"]) || $request["node"] < 1) {
                    $response["message"] = "Invalid node ID";
                    break;
                }

                // Try connecting to the database to save the note
                $dbh = database();

                if ($dbh) {
                    // Retrieve note from the texts table
                    $stmt = $dbh->prepare(
                        "SELECT content FROM texts WHERE nodeID = :node LIMIT 1"
                    );
                    $stmt->bindParam(':node', $request["node"]);
                    $stmt->execute();

                    // Get the note's content for the response
                    $note = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($note) {
                        $response["content"] = $note["content"];
                        $response["success"] = true;
                    }
                }

                break;
            // Update text of note
            case "post:note":
                // Validate request data
                if (!is_numeric($request["node"]) || $request["node"] < 1) {
                    $response["message"] = "Invalid node";
                    break;
                }

                if (!isset($request["text"]) || empty($request["text"])) {
                    $response["message"] = "No content specified";
                    break;
                }

                // Try connecting to the database to save the note
                $dbh = database();

                if ($dbh) {
                    // Create a new node for the node
                    $stmt = $dbh->prepare(
                        "INSERT OR IGNORE INTO nodes " .
                        "(ID, contextID, parentID, type, position) " .
                        "VALUES (:node, 1, NULL, 'text', 0)"
                    );

                    $stmt->bindParam(':node', $request["node"]);
                    $stmt->execute();

                    // Save the note in the texts table
                    $stmt = $dbh->prepare(
                        "REPLACE INTO texts " .
                        "(ID, nodeID, content, lang, version) " .
                        "VALUES (:node, :node, :content, 'en', 0)"
                    );

                    $stmt->bindParam(':node', $request["node"]);
                    $stmt->bindParam(':content', $request["text"]);
                    $stmt->execute();

                    $response["success"] = true;
                }
        }

        // Return response as JSON
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($response);
        break;

    //==========================================================================
    // DEFAULT
    //==========================================================================

    default:
        ?>
<!doctype html>
<html lang="en-US">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1, minimum-scale=1" />
    <meta name="description"
          content="<?= $description ?>" />
    <title><?= $title ?></title>
    <style>
      /* Global rules */
      :root {
        --background-color: rgba(32, 32, 32, 1);
        --surface-color:    rgba(242, 242, 242, 1);
        --accent-color:     rgba(22, 182, 214, 1);
        --primary-color:    rgba(16, 16, 16, 1);
        --secondary-color:  rgba(64, 64, 64, 1);
        --quote-color:      rgba(231, 154, 74, 1);
        --special-color:    rgba(231, 219, 115, 1);
        --number-color:     rgba(145, 128, 255, 1);
        --code-color:       rgba(185, 203, 186, 1);
        --text-width:       70ch;
      }

      * {
        font-family: "Consolas", monospace;
        font-optical-sizing: auto;
      }

      *[contenteditable="true"] {
        background-color: var(--primary-color);
        outline: none;
        user-select: initial;
      }

      body {
        align-items: center;
        background-color: var(--background-color);
        color: var(--surface-color);
        margin: 1em;
        padding: 0;
      }

      main,
      header,
      footer {
        margin: 0 auto;
        width: 100%;
        @media (min-width: 800px) {
          width: var(--text-width);
        }
      }

      h1,
      header > p {
        text-align: center;
      }

      main {
        min-height: 2em;
        padding: .5em;
        text-align: justify;
        white-space: pre;
      }

      a,
      a:active,
      a:visited {
        color: var(--accent-color);
        text-decoration: none;
      }

      a:hover {
        font-weight: bold;
      }

      /* Syntax highlighting */
      q {
        color: var(--quote-color);
      }

      q::before,
      q::after {
        content: '"';
      }

      b {
        color: var(--special-color);
        font-style: normal;
      }

      code {
        background-color: var(--secondary-color);
        border-radius: .2em;
        color: var(--code-color);
        font-size: .9em;
        font-weight: lighter;
        padding: .1em;
      }

      i {
        color: var(--number-color);
        font-style: normal;
      }

      /* Loading animation */
      .loader {
        display: block;
        height: 50px;
        margin: 0 auto;
        position: relative;
        width: 50px;
      }

      .loader:before{
        animation: loader 1.5s linear infinite alternate;
        background: linear-gradient(
          to right, var(--surface-color) 50%,
          var(--accent-color) 50%
        ) no-repeat;
        background-position: 100% 0;
        background-size: 200% auto;
        content: '';
        height: 24px;
        left: 0;
        position: absolute;
        top: 50%;
        transform: translate(-2px, -50%);
        width: 24px;
      }

      @keyframes loader {
        0%  {
          background-position: 0% 0;
          transform: translate(-8px, -50%);
        }

        15%, 25% {
          background-position: 0% 0;
          transform: translate(0px, -50%);
        }

        75%, 85% {
          background-position: 100% 0;
          transform: translate(25px, -50%);
        }

        100% {
          background-position: 100% 0;
          transform: translate(33px, -50%);
        }
      }
    </style>
    <script>
      //========================================================================
      // CONSTANTS
      //========================================================================

      // Requests types
      const requestTypes = {
        "json"           : "application/json",
        "html"           : "text/html",
        "css"            : "text/css",
        "js"             : "text/javascript"
      };

      //========================================================================
      // HELPERS
      //========================================================================

      /**
       * Shortcut for getting an element by selector
       */
      const esel = (sel) => document.querySelector(sel);

      /**
       * Shortcut for sending GET requests with parameters
       * 
       * @param operation Requested operation
       * @param data      Additional request data
       * @param type      Request type (JSON, HTML, CSS, JS)
       * @param cb        Callback function on finish
       */
      const get = (operation, data, type, cb) => {
        // Validate the type of requested content
        if (!requestTypes.hasOwnProperty(type)) {
          console.error("Invalid request type");
          return;
        }

        // Parameters that go with the request, built from the data
        let params = Object.keys(data).map(
          (key, i) => {
            return [key, encodeURI(data[key])].join("=");
          }
        ).join("&");

        if (params) params = "?" + params;

        // Send asynchronous requests
        (async () => {
            const response = await fetch(
              params,
              {
                method: "GET",
                headers: {
                  "Operation": operation,
                  "Accept": requestTypes[type]
                }
              }
            );

            // Wait for the response and return requested type
            switch(type) {
              case "json":
                // Invoke callback with result as JSON
                cb(await response.json());
                break;
              default:
                // Invoke callback with result as DOM template 
                const template = document.createElement("template");
                const result = await response.text();
                template.innerHTML = result.trim();
                cb(template.content);
            }
          }
        )();
      }

      /**
       * Shortcut for sending POST requests with parameters
       * 
       * @param operation Requested operation
       * @param data      Additional request data
       * @param cb        Callback function on finish
       */
      const post = (operation, data, cb) => {
        // Send asynchronous requests
        (async () => {
            const response = await fetch(
              "",
              {
                method: "POST",
                headers: {
                  "Operation": operation,
                  "Accept": "application/json",
                  "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
              }
            );

            // Invoke callback with result as JSON
            cb(await response.json());
          }
        )();
      }

      /**
       * Highlights different special elements of the plain text
       * 
       * @param dom  Plain content container
       * @param text Plain text
       */
      const highlight = (dom, text) => {
        // Regular expression for code
        const codeRegex = new RegExp(
          `\"(?:\\.|[^\\"])*\"|\`([^\`]*?)\``, 'g'
        );

        // Regular expression for numbers
        const numberRegex = new RegExp(
          `"[^"]*"|\\b\\d*\\.?\\d+\\b`, 'g'
        );

        // Regular expression for quoted text
        const doubleQuotedRegex = new RegExp(
          `"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"`, 'g'
        );

        // Regular expression for special characters
        const specialRegex = new RegExp(
          `([^a-zA-z0-9\s<>!?@#$%^&*()-_=+.,;:\/"' \n]+)`,
          'g'
        );

        // Regular expression for links
        const linkRegex = new RegExp(
          `(?:https?:)?:?//` +
          `(?:[a-zA-Z0-9-]+\\.)+` +
          `[a-zA-Z]{2,}` +
          `(?:/[^\\s]*)?`,
          'g'
        );

        // Escape special characters
        text = text.replace("<", "&lt;").replace(">", "&gt;");

        // Change color of code parts
        text = text.replace(
          codeRegex,
          (m, g1) => {
            // Ignore code inside quoted strings
            if (m.startsWith('"')) return m;
            // Wrap back-tick match in <code>
            return `<code>${g1}</code>`;
          }
        );

        // Change color of numbers
        text = text.replace(
          numberRegex,
          m => {
            // If the match is a quoted string, return it unchanged
            if (m.startsWith('"') && m.endsWith('"')) {
              return m;
            }
            // Otherwise, it's a number, so wrap it
            return `<i>${m}</i>`;
          }
        );

        // Change color of double quoted texts
        text = text.replace(
          doubleQuotedRegex,
          (m, g1) => {
            // Replace quoted texts by a q element
            return `<q>${g1}</q>`;
          }
        );

        // Change color of special characters
        text = text.replace(
          specialRegex,
          m => {
            // Replace quoted texts by a q element
            return `<b>${m}</b>`;
          }
        );

        // Replace plain links with anchors
        dom.innerHTML = text.replace(
          linkRegex,
          m => {
            // If the match starts with ://, prepend https
            const href = m.startsWith('://') ? `https${m}` : m;
            return `<a href="${href}" target="_blank">${m}</a>`;
          }
        );
      };

      //========================================================================
      // ACTIONS
      //========================================================================

      /**
       * Entry point
       */
      window.addEventListener("load", () => {
        // Get all editable elements
        const editables = document.querySelectorAll(".editable");

        // Timer for long press events
        let touchTimer;
        // A variable for temporarily storing texts
        let stagedTexts = [];
        // A flag indicating the Escape key has been pressed
        let esc         = false;

        /**
         * Makes the specified element's content editable
         * 
         * @param dom     Element that will become editable
         * @param content Raw editable content
         */
        function makeEditable(dom, content) {
          // Make the content editable and in plain format
          dom.contentEditable = true;
          dom.textContent = content;

          // Reset flags
          esc = false;
        }

        editables.forEach((editable, idx) => {
          // Get content from server
          get("note", { "node": idx + 1 }, "json", (note) => {
            if (note.success) {
              // Set note content, if it was received
              editable.innerText = note.content;
              // Store initial plain texts from note
              stagedTexts[idx] = note.content;
              // Highlight text elements
              highlight(editable, stagedTexts[idx]);
            } else {
              // If no content was received, set empty text
              editable.innerText = "";
              // Store empty text
              stagedTexts[idx] = "";
            }
          });

          // Enable content editing on mouse release after long press
          editable.addEventListener(
            "dblclick",
            (e) => {
              if (editable.contentEditable != "true") {
                makeEditable(editable, stagedTexts[idx]);
              }
            }
          );

          // Prevent browser from adding <br> on Enter key
          editable.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
              e.preventDefault();
              const selection = window.getSelection();
              const range = selection.getRangeAt(0);
              range.deleteContents();
              range.insertNode(document.createTextNode("\n"));
              // Move cursor after the newline
              range.collapse(false);
              selection.removeAllRanges();
              selection.addRange(range);
            }
          });

          // Disable content editing and discard changes on escape key
          editable.addEventListener(
            "keyup",
            (e) => {
              if (e.key == "Escape") {
                esc = true;

                editable.contentEditable = false;
                // Restore initial text
                editable.innerText = stagedTexts[idx];
                // Highlight text elements
                highlight(editable, stagedTexts[idx]);
              }
            }
          );

          // Disable content editing on loosing focus
          editable.addEventListener(
            "blur",
            (e) => {
              editable.contentEditable = false;

              // Update text if something has changed
              if (!esc && (editable.innerText != stagedTexts[idx])) {
                // Update staged text with changes
                stagedTexts[idx] = editable.innerText;

                // Send request to the server
                post(
                  "note",
                  {
                    "text": editable.innerText,
                    "node": idx + 1
                  },
                  (result) => {
                    if (!result.success) alert("Failed to save text");
                  }
                );
              }

              // Discard selected texts
              window.getSelection().removeAllRanges();

              // Highlight text elements
              highlight(editable, stagedTexts[idx]);
            }
          );

          // Insert clean, plain text on paste event
          editable.addEventListener(
            "paste",
            (e) => {
              e.preventDefault();

              // Get text from the clipboard
              const text = (
                event.clipboardData || window.clipboardData
              ).getData('text/plain');

              // Get the current selection
              const selection = window.getSelection();
              if (!selection.rangeCount) return;

              // Insert the text at the cursor
              const range = selection.getRangeAt(0);

              // Remove any selected content
              range.deleteContents();

              // Insert pasted text
              const pastedText = document.createTextNode(text);
              range.insertNode(pastedText);

              // Move cursor after the inserted text
              range.setStartAfter(pastedText);
              range.setEndAfter(pastedText);
              selection.removeAllRanges();
              selection.addRange(range);
            }
          );
        });
      });
    </script>
  </head>
  <body>
    <header>
      <h1 class="editable"><?= $title ?></h1>
      <p class="editable"><?= $description ?></p>
    </header>
    <main class="editable">
      <span class="loader"></span>
    </main>
    <footer>
    </footer>
    <dialog>
    </dialog>
  </body>
</html>
        <?php
endswitch;
?>
