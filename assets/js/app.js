/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';
import "bootstrap";

import "highlight.js/styles/monokai.css";
import "codemirror/mode/markdown/markdown";
import "codemirror/mode/xml/xml";
import "codemirror/mode/javascript/javascript";
import "codemirror/mode/ruby/ruby";
import "codemirror/mode/python/python";
import "codemirror/mode/php/php";
import "codemirror/mode/yaml/yaml";
import "codemirror/mode/clike/clike";
import CodeMirror from "codemirror";
import hljs from "highlight.js";

import $ from "jquery";

import * as utils from "./utils";

hljs.initHighlightingOnLoad();

$(() => {
    const codeArea = $(".code");
    if (codeArea.length > 0) {
        CodeMirror.fromTextArea(codeArea[0], {
            lineNumbers: true,
            mode: "markdown",
            theme: "monokai"
        });
    }
    $("[data-method]").on("click", utils.ajaxLinkHandler);
});