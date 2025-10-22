<?php

/* @Twig/Exception/error404.html.twig */
class __TwigTemplate_c4555b4c601d1efb676b2040ca3f5ec1c8e3305ec790e777182fe6497a34121a extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "@Twig/Exception/error404.html.twig", 1);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "
    <div class=\"container\">

        <div class=\"col-sm-8 margin_100  mx-auto\">

            <div class=\"font-xl\">

                <img src=\"";
        // line 11
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("assets/images/logo.png"), "html", null, true);
        echo "\" class=\"mb-5\" style=\"max-width: 300px\">
                    <h1 class=\"font-xl-3 font-weight-100 font-red\">Error ";
        // line 12
        echo twig_escape_filter($this->env, ($context["status_code"] ?? null), "html", null, true);
        echo "</h1>
                <p class=\"font-xl\">";
        // line 13
        echo twig_escape_filter($this->env, ($context["status_text"] ?? null), "html", null, true);
        echo ". Click here to <a href=\"javascript: window.history.go(-1)\">Go back</a></p>

            </div>

        </div>



    </div>
";
    }

    public function getTemplateName()
    {
        return "@Twig/Exception/error404.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  48 => 13,  44 => 12,  40 => 11,  31 => 4,  28 => 3,  11 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Twig/Exception/error404.html.twig", "/home/federalc/public_html/training/templates/bundles/TwigBundle/Exception/error404.html.twig");
    }
}
