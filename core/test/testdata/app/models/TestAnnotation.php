<?php
/**
 * @Inject
 * @Database("diarysys")
 * @SQL("db/user.properties")
 */
class TestAnnotation extends CoreModel2 {
    
    /**
     * @Inject
     * @Hogehoge("test")
     */
    public function hogehoge() {
        
    }
    
}
