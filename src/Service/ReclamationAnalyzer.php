<?php

namespace App\Service;

class ReclamationAnalyzer
{
    private const URGENT_KEYWORDS = ['urgent', 'immédiat', 'critique', 'grave', 'important'];
    private const NEGATIVE_KEYWORDS = ['insatisfait', 'mécontent', 'problème', 'mauvais', 'défaut'];

    public function analyzeReclamation(string $text): array
    {
        $text = strtolower($text);
        $words = str_word_count($text, 1, 'àáâãäçèéêëìíîïñòóôõöùúûüýÿ');
        
        // Calcul du score d'urgence
        $urgencyScore = $this->calculateKeywordScore($words, self::URGENT_KEYWORDS);
        
        // Calcul du score de sentiment
        $sentimentScore = $this->calculateSentimentScore($words);
        
        // Calcul de la priorité
        $priority = $this->calculatePriority($urgencyScore, $sentimentScore);
        
        // Classification par type
        $category = $this->classifyReclamation($text);
        
        return [
            'urgency_score' => $urgencyScore,
            'sentiment_score' => $sentimentScore,
            'priority' => $priority,
            'category' => $category,
            'suggested_response' => $this->suggestResponse($category, $priority)
        ];
    }

    private function calculateKeywordScore(array $words, array $keywords): float
    {
        $score = 0;
        $totalWords = count($words);
        
        foreach ($words as $word) {
            if (in_array($word, $keywords)) {
                $score++;
            }
        }
        
        return ($totalWords > 0) ? ($score / $totalWords) * 100 : 0;
    }

    private function calculateSentimentScore(array $words): float
    {
        $negativeScore = $this->calculateKeywordScore($words, self::NEGATIVE_KEYWORDS);
        return 100 - $negativeScore; // Plus le score est bas, plus le sentiment est négatif
    }

    private function calculatePriority(float $urgencyScore, float $sentimentScore): string
    {
        $totalScore = ($urgencyScore * 0.7) + ($sentimentScore * 0.3);
        
        if ($totalScore >= 80) return 'FAIBLE';
        if ($totalScore >= 50) return 'MOYENNE';
        return 'HAUTE';
    }

    private function classifyReclamation(string $text): string
    {
        // Définition des catégories et leurs mots-clés associés
        $categories = [
            'TECHNIQUE' => ['bug', 'erreur', 'problème technique', 'ne fonctionne pas', 'panne'],
            'SERVICE' => ['service', 'délai', 'attente', 'réponse', 'support'],
            'PRODUIT' => ['produit', 'qualité', 'défaut', 'marchandise', 'article'],
            'LIVRAISON' => ['livraison', 'retard', 'colis', 'expédition', 'transport']
        ];

        $maxScore = 0;
        $bestCategory = 'AUTRE';

        foreach ($categories as $category => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    $score++;
                }
            }
            if ($score > $maxScore) {
                $maxScore = $score;
                $bestCategory = $category;
            }
        }

        return $bestCategory;
    }

    private function suggestResponse(string $category, string $priority): string
    {
        $responses = [
            'TECHNIQUE' => [
                'HAUTE' => 'Nous prenons en charge votre problème technique en urgence. Notre équipe technique a été notifiée et interviendra dans les plus brefs délais.',
                'MOYENNE' => 'Notre équipe technique analyse votre problème et reviendra vers vous avec une solution rapidement.',
                'FAIBLE' => 'Nous avons bien noté votre signalement technique. Notre équipe l\'examinera dès que possible.'
            ],
            'SERVICE' => [
                'HAUTE' => 'Nous sommes désolés pour ce désagrément. Un responsable service client va prendre en charge votre demande en priorité.',
                'MOYENNE' => 'Votre satisfaction est notre priorité. Un conseiller traitera votre demande dans les 24h.',
                'FAIBLE' => 'Nous avons bien enregistré votre retour concernant nos services. Nous y accorderons toute notre attention.'
            ],
            'PRODUIT' => [
                'HAUTE' => 'Nous prenons très au sérieux votre réclamation concernant le produit. Un expert produit va examiner votre cas immédiatement.',
                'MOYENNE' => 'Notre service qualité va analyser votre retour produit et vous recontacter rapidement.',
                'FAIBLE' => 'Nous vous remercions pour votre retour concernant le produit. Nous l\'étudierons avec attention.'
            ],
            'LIVRAISON' => [
                'HAUTE' => 'Nous nous excusons pour ce problème de livraison. Nous le traitons en priorité et revenons vers vous très rapidement.',
                'MOYENNE' => 'Votre problème de livraison est pris en compte. Notre service logistique s\'en occupe.',
                'FAIBLE' => 'Nous avons bien noté votre remarque concernant la livraison et allons l\'examiner.'
            ],
            'AUTRE' => [
                'HAUTE' => 'Votre réclamation est prise en compte et sera traitée en priorité.',
                'MOYENNE' => 'Nous avons bien reçu votre réclamation et la traiterons dans les meilleurs délais.',
                'FAIBLE' => 'Nous vous remercions pour votre retour. Nous l\'examinerons prochainement.'
            ]
        ];

        return $responses[$category][$priority] ?? $responses['AUTRE'][$priority];
    }
}
