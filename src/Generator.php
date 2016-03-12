<?php
/**
 * Created by PhpStorm.
 * User: manželé Jiráskovi
 * Date: 12.3.2016
 * Time: 8:49
 */

namespace Zarganwar\NextrasOrmUtils;

use Nette\PhpGenerator\ClassType;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PHPSQLParser\PHPSQLParser;

class Generator
{
    const MAPPER = 'mapper';
    const REPOSITORY = 'repository';
    const ENTITY = 'entity';

    /**
     * @var string
     */
    private $target;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var array
     */
    public $parsedSql;


    /**
     * Generator constructor.
     * @param string $target
     * @param string $namespace
     */
    public function __construct($target, $namespace = null)
    {
        $this->target = $target;
        $this->namespace = $namespace;
    }

    /**
     * @param string $sql
     */
    public function build($sql)
    {
        $this->parseSql($sql);
        $this->create();
    }

    /**
     * @param string $sql
     */
    public function parseSql($sql)
    {
        $parser = new PHPSQLParser();
        $result = preg_match_all("/(CREATE [.\\s\\w\\W]*;)/U", $sql, $matches);
        if ($result !== false) {
            foreach ($matches[0] as $match) {
                $this->parsedSql[] = $parser->parse($match);
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function create()
    {
        $this->createOrmDir();
        $this->createModel();
        $this->createLayerClasses();
    }

    private function createOrmDir()
    {
        FileSystem::createDir($this->target);
    }

    private function createModel()
    {
        $class = new ClassType('Orm', $this->namespace);
        $class->setExtends('\Nextras\Orm\Model\Model');

        foreach ($this->parsedSql as $block) {
            if (isset($block['CREATE'], $block['TABLE'])) {
                $table = $block['TABLE']['name'];
                $type = $this->getCamelCase($table) . "Repository";
                $property = '$' . $this->getCamelCase($table, true) . 's';
                $class->addDocument("@property-read $type $property");
            }
        }
        $this->createClass($class);
    }


    /**
     * @param string $tableName
     * @param bool $firstLower
     * @return string
     */
    private function getCamelCase($tableName, $firstLower = false)
    {
        $exploded = explode('_', $tableName);
        array_walk($exploded, function(&$part) {$part = ucfirst($part);});
        $return = implode('', $exploded);
        if ($firstLower) {
            $return = lcfirst($return);
        }
        return $return;
    }

    /**
     * @param $className
     */
    private function createEntity($className)
    {
        $class = new ClassType($className, $this->namespace);
        $class->setExtends('Nextras\Orm\Entity\Entity');
        $this->createClass($class, $className);
    }

    /**
     * @param $className
     */
    private function createRepository($className)
    {
        $postfix = 'Repository';

        $class = new ClassType($className . $postfix, $this->namespace);
        $class->setExtends('Nextras\Orm\Repository\Repository');

        $class
            ->addMethod("getEntityClassNames")
            ->setStatic(true)
            ->setVisibility('public')
            ->addDocument("@return array")
            ->addBody("return [$className::class];")
        ;
        $this->createClass($class, $className);
    }

    /**
     * @param $className
     */
    private function createMapper($className)
    {
        $postfix = 'Mapper';
        $class = new ClassType($className . $postfix, $this->namespace);
        $class->setExtends('Nextras\Orm\Mapper\Mapper');
        $this->createClass($class, $className);

    }

    /**
     * @param ClassType $classType
     * @param string $dir
     */
    private function createClass(ClassType $classType, $dir = "")
    {
        if ($dir) {
            $dir = "/$dir";
        }

        $filePath = $this->target . "$dir/" . $classType->getName() . ".php";
        if (!file_exists($filePath)) {
            file_put_contents($filePath, "<?php" . PHP_EOL . PHP_EOL . $classType->__toString());
        }
    }

    /**
     * @param $name
     */
    private function createLayerDir($name)
    {
        FileSystem::createDir($this->target . "/$name");
    }

    private function createLayerClasses()
    {
        foreach ($this->parsedSql as $block) {
            if (isset($block['CREATE'], $block['TABLE'])) {
                $className = $this->getCamelCase($block['TABLE']['name']);
                $this->createLayerDir($className);
                $this->createEntity($className);
                $this->createMapper($className);
                $this->createRepository($className);
            }
        }
    }

}
