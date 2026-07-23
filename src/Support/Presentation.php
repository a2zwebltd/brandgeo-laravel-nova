<?php

namespace A2ZWeb\BrandGeoNova\Support;

use A2ZWeb\BrandGeoClient\Enums\Provider;

/**
 * Presentation maps mirroring BrandGEO's own dashboard
 * (App\Support\AuditReportPresentation + ScoreColors + AuditProvider) so the
 * Nova dashboard renders API data with identical labels, order and colours.
 */
class Presentation
{
    /** short key => full result_json key, in render order */
    public const SECTIONS = [
        'a_recognition' => 'section_a_recognition',
        'b_knowledge_depth' => 'section_b_knowledge_depth',
        'c_competitive_context' => 'section_c_competitive_context',
        'd_sentiment_authority' => 'section_d_sentiment_authority',
        'e_contextual_recall' => 'section_e_contextual_recall',
        'f_ai_discoverability' => 'section_f_ai_discoverability',
    ];

    public const SECTION_LABELS = [
        'a_recognition' => 'Recognition',
        'b_knowledge_depth' => 'Knowledge Depth',
        'c_competitive_context' => 'Competitive Context',
        'd_sentiment_authority' => 'Sentiment & Authority',
        'e_contextual_recall' => 'Contextual Recall',
        'f_ai_discoverability' => 'AI Discoverability',
    ];

    public const SECTION_DESCRIPTIONS = [
        'a_recognition' => 'Does the AI know your brand exists? Basic awareness like name, founding date, and location.',
        'b_knowledge_depth' => 'How well does the AI understand your products, features, audience, and tech stack?',
        'c_competitive_context' => 'Is your brand mentioned alongside competitors? Category association and market positioning.',
        'd_sentiment_authority' => 'What does the AI think about your brand? Sentiment, credibility, and founder recognition.',
        'e_contextual_recall' => 'Does the AI bring up your brand in relevant conversations? Topical and geographic recall.',
        'f_ai_discoverability' => 'How likely is the AI to recommend your brand? Name uniqueness, domain signals, and training presence.',
    ];

    /** Tailwind accent per dimension (kept in sync with BrandGEO). */
    public const SECTION_ACCENTS = [
        'a_recognition' => 'sky',
        'b_knowledge_depth' => 'amber',
        'c_competitive_context' => 'violet',
        'd_sentiment_authority' => 'teal',
        'e_contextual_recall' => 'pink',
        'f_ai_discoverability' => 'indigo',
    ];

    public const FIELD_LABELS = [
        'a1_brand_recognized' => 'Brand Recognized',
        'a2_company_name' => 'Company Name',
        'a3_founding_date' => 'Founding Date',
        'a4_headquarters_location' => 'Headquarters',
        'b1_product_description' => 'Product Description',
        'b2_features' => 'Features',
        'b3_target_audience' => 'Target Audience',
        'b4_tech_stack' => 'Tech Stack',
        'c1_top_of_mind' => 'Top of Mind',
        'c2_competitors' => 'Competitors',
        'c3_category_association' => 'Category Association',
        'd1_sentiment' => 'Sentiment',
        'd2_external_validation' => 'External Validation',
        'd3_open_source_presence' => 'Open Source Presence',
        'd4_content_authority' => 'Content Authority',
        'd5_founder_or_team_recognition' => 'Founder / Team',
        'e1_conversation_contexts' => 'Conversation Contexts',
        'e2_geographic_relevance' => 'Geographic Relevance',
        'f1_brand_name_uniqueness' => 'Name Uniqueness',
        'f2_domain_authority_signals' => 'Domain Signals',
        'f3_api_or_integration_presence' => 'API / Integrations',
        'f4_training_data_presence_estimate' => 'Training Data Presence',
        'f5_llm_recommendation_likelihood' => 'LLM Recommendation',
    ];

    public const FINDING_CATEGORY_LABELS = [
        'recognition' => 'Recognition',
        'knowledge' => 'Knowledge',
        'competitive' => 'Competitive',
        'sentiment' => 'Sentiment',
        'recall' => 'Recall',
        'discoverability' => 'Discoverability',
        'strength' => 'Strength',
        'weakness' => 'Weakness',
        'opportunity' => 'Opportunity',
    ];

    /** sentiment => [accent border, icon] for finding cards */
    public const FINDING_SENTIMENT = [
        'positive' => ['border-emerald-500/40', 'text-emerald-600 dark:text-emerald-400', '▲'],
        'neutral' => ['border-amber-500/40', 'text-amber-600 dark:text-amber-400', '◆'],
        'negative' => ['border-red-500/40', 'text-red-600 dark:text-red-400', '▼'],
    ];

    public const CONFIDENCE_CLASSES = [
        'KNOW' => 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
        'PARTIAL' => 'bg-blue-500/15 text-blue-700 dark:text-blue-300',
        'GUESSING' => 'bg-amber-500/15 text-amber-700 dark:text-amber-300',
        'UNKNOWN' => 'bg-zinc-500/15 text-zinc-600 dark:text-zinc-400',
    ];

    public const PROVIDER_COLORS = [
        'openai' => '#10b981',
        'anthropic' => '#d4a574',
        'gemini' => '#3b82f6',
        'xai' => '#8b5cf6',
        'deepseek' => '#f59e0b',
    ];

    public const PROVIDER_LABELS = [
        'openai' => 'OpenAI',
        'anthropic' => 'Claude (Anthropic)',
        'gemini' => 'Gemini (Google)',
        'xai' => 'Grok (xAI)',
        'deepseek' => 'DeepSeek',
    ];

    /** Share-of-voice competitor palette (brand itself is always #3b82f6). */
    public const SOV_PALETTE = ['#a1a1aa', '#8b5cf6', '#f59e0b', '#ef4444', '#10b981', '#6366f1', '#ec4899', '#14b8a6', '#f97316'];

    public const CATEGORY_DESCRIPTIONS = [
        'discovery' => 'How easily the brand is found when users ask about the industry',
        'comparison' => 'How the brand is positioned against competitors',
        'recommendation' => 'Whether AI recommends the brand for relevant queries',
        'sentiment' => 'The overall tone and perception AI conveys about the brand',
        'feature' => "How well AI knows the brand's key features and offerings",
        'use_case' => 'Whether AI associates the brand with relevant use cases',
    ];

    public static function providerColor(Provider|string $provider): string
    {
        $key = $provider instanceof Provider ? $provider->value : $provider;

        return self::PROVIDER_COLORS[$key] ?? '#a1a1aa';
    }

    public static function providerLabel(Provider|string $provider): string
    {
        $key = $provider instanceof Provider ? $provider->value : $provider;

        return self::PROVIDER_LABELS[$key] ?? ucfirst($key);
    }

    /**
     * The 0–100 scale as label => range, low→high, mirroring BrandGEO's
     * ScoreColors::scaleLegend(). Drives the always-visible score-scale bar.
     * The bottom band reads "Low" rather than the app's "Critical" — this
     * dashboard sits in the client's own admin, where a neutral word beats an
     * alarming one.
     *
     * @var array<string, array{string, int}> label => [range, representative mid-score]
     */
    public const SCORE_BANDS = [
        'Low' => ['0–19', 10],
        'Weak' => ['20–39', 30],
        'Fair' => ['40–59', 50],
        'Strong' => ['60–79', 70],
        'Excellent' => ['80–100', 90],
    ];

    /**
     * BrandGEO ScoreColors bands. Returns [textClass, barClass, hex, label].
     * Text/bar classes carry both themes (Nova's light mode and dark mode) —
     * the *-400 shades alone wash out on a white surface.
     */
    public static function score(?float $score): array
    {
        return match (true) {
            $score === null => ['text-zinc-400 dark:text-zinc-500', 'bg-zinc-300 dark:bg-zinc-600', '#71717a', '—'],
            $score >= 80 => ['text-green-600 dark:text-green-400', 'bg-green-500 dark:bg-green-400', '#4ade80', 'Excellent'],
            $score >= 60 => ['text-emerald-600 dark:text-emerald-400', 'bg-emerald-500 dark:bg-emerald-400', '#34d399', 'Strong'],
            $score >= 40 => ['text-yellow-600 dark:text-yellow-400', 'bg-yellow-500 dark:bg-yellow-400', '#facc15', 'Fair'],
            $score >= 20 => ['text-orange-600 dark:text-orange-400', 'bg-orange-500 dark:bg-orange-400', '#fb923c', 'Weak'],
            default => ['text-red-600 dark:text-red-400', 'bg-red-500 dark:bg-red-400', '#f87171', 'Low'],
        };
    }

    /** Normalize a section_scores entry (scalar or {score}) to a float|null. */
    public static function sectionScore(mixed $entry): ?float
    {
        if (is_numeric($entry)) {
            return (float) $entry;
        }

        if (is_array($entry) && isset($entry['score'])) {
            return (float) $entry['score'];
        }

        return null;
    }

    /** The BrandGEO app root for deep links, derived from the API base_url unless overridden. */
    public static function appUrl(): string
    {
        if ($url = config('brandgeo-nova.app_url')) {
            return rtrim($url, '/');
        }

        return rtrim(preg_replace('#/api/v\d+/?$#', '', (string) config('brandgeo-client.base_url')), '/');
    }
}
