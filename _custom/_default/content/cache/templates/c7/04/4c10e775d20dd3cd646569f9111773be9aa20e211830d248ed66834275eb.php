<?php

/* index.html */
class __TwigTemplate_c7044c10e775d20dd3cd646569f9111773be9aa20e211830d248ed66834275eb extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<html>
<head>
\t<base href=\"\">
\t\t
\t<!-- JQuery -->
\t<script src=\"http://code.jquery.com/jquery-2.1.1.min.js\"></script>
\t\t
\t<!-- Bootstrap -->
\t<!-- Latest compiled and minified CSS -->
\t<link rel=\"stylesheet\" href=\"//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css\">
\t<!-- Optional theme -->
\t<link rel=\"stylesheet\" href=\"//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css\">
\t<!-- Latest compiled and minified JavaScript -->
\t<script src=\"//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js\"></script>
\t
\t<!-- Our stylesheet - overrides -->
\t<link href=\"style.css\" rel=stylesheet type=\"text/css\">
\t
</head>
<body>
\t<p>Hello. I am your Twig template</p>
</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "index.html";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
