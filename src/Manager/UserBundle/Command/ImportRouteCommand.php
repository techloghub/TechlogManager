<?php
namespace Manager\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Manager\UserBundle\Entity\Path;

class ImportRouteCommand extends ContainerAwareCommand {
    protected function configure()
    {
        $this
            ->setName('user:import:route')
            ->setDescription('import route')
            ->addArgument('file', InputArgument::REQUIRED, 'file: csv file contain route')
            ->addOption('delete', 'd', InputOption::VALUE_OPTIONAL, 'delete the item which have the same name', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        if (pathinfo($file, PATHINFO_EXTENSION) != 'csv') {
            echo "file error";
            exit;
        }
        $delete = $input->getOption('delete');

        $em = $this->getContainer()->get('doctrine')->getManager('default');

        $category1_id = 0;

        $handle = fopen($file, 'r');
        while ($data = fgetcsv($handle, 1000, ',')) {
            $data = array_filter($data);

            if (count($data) == 3) {
                //二级目录
                $route = $data[0];
                $category1_name = $data[1];
                $category2_name = $data[2];
                $category1_name = iconv("GB2312", "UTF8", $category1_name);
                $category2_name = iconv("GB2312", "UTF8", $category2_name);

                //判断一级目录是否存在
                $entity = $em->getRepository('ManagerUserBundle:Path')->findOneBy(array('name' => $category1_name));

                if (empty($entity)) {
                    echo "category1 not exist";
                    exit;
                }
                $category1_id = $entity->getId();

                // 判断路由是不是存在
                $entitys = $em->getRepository('ManagerUserBundle:Path')->findBy(array('route' => $route));

                if (!empty($entitys)) {
                    if ($delete == 1) {
                        foreach ($entitys as $entity) {
                            $em->remove($entity);
                        }
                        $em->flush();
                    } else {
                        echo 'route exist';
                        exit;
                    }
                }
                $name = "{$category1_name}_{$category2_name}";

                $entity = new Path();
                $entity->setName($category2_name)
                    ->setFirstMenu($category1_id)
                    ->setSecondMenu(0)
                    ->setRoute($route)
                    ->setRemark($name)
                    ->setOperator("user:import:route")
                    ->setUpdateTime(new \DateTime())
                    ->setCreateTime(new \DateTime());
                $em->persist($entity);
                $em->flush();

                echo "创建二级分类 {$category2_name} 成功\r\n";
            }

            if (count($data) == 4) {
                //三级目录
                $route = $data[0];
                $category1_name = $data[1];
                $category2_name = $data[2];
                $category3_name = $data[3];
                $category1_name = iconv("GB2312", "UTF8", $category1_name);
                $category2_name = iconv("GB2312", "UTF8", $category2_name);
                $category3_name = iconv("GB2312", "UTF8", $category3_name);

                //判断一级目录是否存在
                $entitys = $em->getRepository('ManagerUserBundle:Path')->findBy(array('name' => $category1_name));
                if (count($entitys) != 1) {
                    echo "{$category1_name} 有错误";
                    exit;
                }
                $category1_id = $entitys[0]->getId();

                //判断二级目录是否存在
                $entitys = $em->getRepository('ManagerUserBundle:Path')->findBy(array('name' => $category2_name));
                if (count($entitys) != 1) {
                    echo "{$category2_name} 有错误";
                    exit;
                }
                $category2_id = $entitys[0]->getId();

                //判断

                // 判断路由是不是存在
                $entitys = $em->getRepository('ManagerUserBundle:Path')->findBy(array('route' => $route));
                if (!empty($entitys)) {
                    if ($delete == 1) {
                        foreach ($entitys as $entity) {
                            $em->remove($entity);
                        }
                        $em->flush();
                    } else {
                        echo "{$route} 已经存在";
                        exit;
                    }
                }
                $name = "{$category1_name}_{$category2_name}_{$category3_name}";

                $entity = new Path();
                $entity->setName($name)
                    ->setFirstMenu($category1_id)
                    ->setSecondMenu($category2_id)
                    ->setRoute($route)
                    ->setRemark($name)
                    ->setOperator("user:import:route")
                    ->setUpdateTime(new \DateTime())
                    ->setCreateTime(new \DateTime());
                $em->persist($entity);
                $em->flush();

                echo "创建三级分类 {$name} 成功\r\n";
            }
        }
        fclose($handle);
    }

}
