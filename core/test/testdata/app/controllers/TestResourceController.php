<?php
namespace WebStream;
class TestResourceController extends CoreController {
    /**
     * @Inject
     * @Render("html.tmpl")
     */
    public function html() {}
    
    /**
     * @Inject
     * @Render("rss.tmpl")
     * @Format("rss")
     */
    public function rss() {}
    
    /**
     * @Inject
     * @Render("xml.tmpl")
     * @Format("xml")
     */
    public function xml() {}
    
    /**
     * @Inject
     * @Render("rdf.tmpl")
     * @Format("rdf")
     */
    public function rdf() {}
    
    /**
     * @Inject
     * @Render("atom.tmpl")
     * @Format("atom")
     */
    public function atom() {}
}
