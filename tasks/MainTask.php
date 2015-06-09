<?php

use Goutte\Client;

class MainTask extends \Phalcon\CLI\Task {
  public function mainAction() {
    echo "It works!!! \n";
  }

  public function linkAction() {
    $client = new Client();
    $file = file_get_contents("/media/psf/Home/Documents/zbornica.html");
    $host = "http://katalogi.gzs.si/";

    $dom = new DOMDocument("1.0");
    $dom->loadHTML($file);

    $finder = new DOMXPath($dom);
    $anchors = $finder->query('//a[@title="Podrobnosti o podjetju"]');
    $data = array();
    foreach ($anchors as $element) {
      $href = $element->getAttribute('href');
      $titles = $finder->query("//a[@href='$href']");
       $number = substr($href, -7);
      foreach ($titles as $title) {
        $data[$number] = $title->nodeValue;
      }
    }
    $results = fopen("results.xml", "w");
    fwrite ($results, '<?xml version="1.0" encoding="UTF-8"?>'."\n");
    fwrite($results, '<Root>'."\n");
    $i = 0;
    foreach ($anchors as $element) {
      fwrite($results, '<Agency>'."\n");
      $href = $element->getAttribute('href');
      $number = substr($href, -7);
      fwrite($results, "<Name>".$data[$number]."</Name>"."\n");
      if (substr($href, -19, -12)=="kat=029") {
        $crawler = $client->request("GET", $host.$href);
        $crawler->filter(".text_blue_r")->each(function (\Symfony\Component\DomCrawler\Crawler $node) use ($number,
          $results) {
          if (strpos($node->text(), "Elektronski")!==false) {
            $email = substr($node->text(), 31);
            fwrite($results, "<Email>".trim($email)."</Email>"."\n");
          }
          else if (strpos($node->text(), "WWW")!==false) {
            $www = strstr($node->text(), "www");
            fwrite($results, "<Webpage>".trim($www)."</Webpage>"."\n");
          }
          else if (strpos($node->text(), "Glavna")!==false) {
            $main = substr($node->text(), 38);
            fwrite($results, "<Djelatnost>".trim($main)."</Djelatnost>"."\n");
          }
          else if (strpos($node->text(), "Poslo")!==false) {
            $bsns = substr($node->text(), 24);
            fwrite($results, "<Poslovnice>".trim($bsns)."</Poslovnice>"."\n");
          }
        });
      }
      fwrite ($results, '</Agency>'."\n");
      $i++;
      echo $i."\n";
    }
    fwrite ($results, '</Root>');

  }
}

