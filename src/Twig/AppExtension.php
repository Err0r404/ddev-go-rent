<?php
namespace App\Twig;

use App\Service\Securizer;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(private Securizer $securizer)
    {
    }
    
    public function getFilters(): array
    {
        return [
            new TwigFilter('securizer', [$this, 'securizer']),
            new TwigFilter('pluralize', [$this, 'pluralize']),
            new TwigFilter('human_readable', [$this, 'humanReadable']),
        ];
    }
    
    public function securizer(UserInterface $user, string $role): bool
    {
        return $this->securizer->isGranted($user, $role);
    }
    
    public function pluralize(int $count, string $singular, string $plural, string $zero = null): string
    {
        if ($count > 1){
            return str_replace('{}', $count, $plural);
        } else if ($count <= 0 && null !== $zero){
            return $zero; // No string replacement required for zero
        }
        return str_replace('{}', $count, $singular);
    }
    
    public function humanReadable($number, $precision = 1): int|string
    {
        // the length of the n
        $len = strlen($number);
        
        // getting the rest of the numbers
        $rest = (int)substr($number, $precision + 1, $len);
        
        // checking if the numbers is integer yes add + if not nothing
        $checkPlus = (is_int($rest) and !empty($rest)) ? "+" : "";
        
        $n_format = $suffix = '';
        if ($number >= 0 && $number < 1000) {
            // 1 - 999
            $n_format = number_format($number);
        }
        elseif ($number >= 1000 && $number < 1000000) {
            // 1k-999k
            $n_format = number_format($number / 1000, $precision);
            $suffix   = 'K' . $checkPlus;
        }
        elseif ($number >= 1000000 && $number < 1000000000) {
            // 1m-999m
            $n_format = number_format($number / 1000000, $precision);
            $suffix   = 'M' . $checkPlus;
        }
        elseif ($number >= 1000000000 && $number < 1000000000000) {
            // 1b-999b
            $n_format = number_format($number / 1000000000);
            $suffix   = 'B' . $checkPlus;
        }
        elseif ($number >= 1000000000000) {
            // 1t+
            $n_format = number_format($number / 1000000000000);
            $suffix   = 'T' . $checkPlus;
        }
        
        return !empty($n_format . $suffix) ? $this->mny2($n_format) . $suffix : 0;
    }
    
    private function mny2($value): string
    {
        $value = sprintf("%0.1f", $value);
        $vl    = explode(".", $value);
        $value = number_format($vl[0]);
        if ($vl[1] != "0") $value .= "." . $vl[1];
        return $value;
    }
}
