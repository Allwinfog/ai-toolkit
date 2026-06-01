<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Plans ──
        $free = Plan::create([
            'name' => 'Free', 'slug' => 'free', 'description' => 'Get started with AI generation',
            'price' => 0, 'billing_cycle' => 'monthly',
            'text_generations' => 10, 'image_generations' => 5, 'code_generations' => 10,
            'words_per_generation' => 1000, 'sort_order' => 0,
            'features' => ['Basic templates', 'Generation history', 'Community support'],
        ]);

        Plan::create([
            'name' => 'Starter', 'slug' => 'starter', 'description' => 'For individuals and freelancers',
            'price' => 9.99, 'billing_cycle' => 'monthly',
            'text_generations' => 100, 'image_generations' => 50, 'code_generations' => 100,
            'words_per_generation' => 3000, 'is_featured' => false, 'sort_order' => 1,
            'features' => ['All templates', 'GPT-4o Mini', 'DALL-E 3', 'Email support'],
        ]);

        Plan::create([
            'name' => 'Pro', 'slug' => 'pro', 'description' => 'For teams and agencies',
            'price' => 29.99, 'billing_cycle' => 'monthly',
            'text_generations' => 500, 'image_generations' => 200, 'code_generations' => 500,
            'words_per_generation' => 5000, 'is_featured' => true, 'sort_order' => 2,
            'features' => ['All templates', 'GPT-4o access', 'DALL-E 3 HD', 'Priority support', 'API access'],
        ]);

        Plan::create([
            'name' => 'Unlimited', 'slug' => 'unlimited', 'description' => 'No limits, maximum power',
            'price' => 79.99, 'billing_cycle' => 'monthly',
            'text_generations' => -1, 'image_generations' => -1, 'code_generations' => -1,
            'words_per_generation' => 10000, 'sort_order' => 3,
            'features' => ['Unlimited everything', 'All models', 'Custom templates', 'Dedicated support', 'White-label'],
        ]);

        // ── Admin User ──
        User::create([
            'name' => 'Admin', 'email' => 'admin@aitoolkit.com',
            'password' => Hash::make('password'), 'role' => 'admin',
            'plan_id' => $free->id, 'email_verified_at' => now(),
        ]);

        // ── Templates ──
        $templates = [
            [
                'name' => 'Blog Post Writer', 'slug' => 'blog-post', 'category' => 'blog', 'type' => 'text',
                'description' => 'Generate SEO-optimized blog posts on any topic',
                'icon' => 'file-text',
                'system_prompt' => 'You are an expert blog writer. Write engaging, SEO-optimized blog posts with proper headings, subheadings, and a compelling introduction.',
                'user_prompt_template' => 'Write a {{length}} word blog post about "{{topic}}". Tone: {{tone}}. Target audience: {{audience}}.',
                'fields' => [
                    ['name' => 'topic', 'label' => 'Blog Topic', 'type' => 'text', 'placeholder' => 'e.g., Benefits of remote work', 'required' => true],
                    ['name' => 'length', 'label' => 'Word Count', 'type' => 'select', 'options' => ['500', '800', '1200', '2000'], 'default' => '800'],
                    ['name' => 'tone', 'label' => 'Tone', 'type' => 'select', 'options' => ['Professional', 'Casual', 'Humorous', 'Academic'], 'default' => 'Professional'],
                    ['name' => 'audience', 'label' => 'Target Audience', 'type' => 'text', 'placeholder' => 'e.g., Small business owners'],
                ],
            ],
            [
                'name' => 'Social Media Post', 'slug' => 'social-media', 'category' => 'social', 'type' => 'text',
                'description' => 'Create engaging social media content for any platform',
                'icon' => 'share-2',
                'system_prompt' => 'You are a social media expert. Create engaging, platform-optimized posts with appropriate hashtags and calls to action.',
                'user_prompt_template' => 'Create a {{platform}} post about "{{topic}}". Goal: {{goal}}. Include relevant hashtags.',
                'fields' => [
                    ['name' => 'topic', 'label' => 'Topic', 'type' => 'text', 'placeholder' => 'e.g., Product launch announcement', 'required' => true],
                    ['name' => 'platform', 'label' => 'Platform', 'type' => 'select', 'options' => ['Twitter/X', 'LinkedIn', 'Instagram', 'Facebook', 'TikTok'], 'default' => 'LinkedIn'],
                    ['name' => 'goal', 'label' => 'Goal', 'type' => 'select', 'options' => ['Engagement', 'Traffic', 'Brand awareness', 'Sales'], 'default' => 'Engagement'],
                ],
            ],
            [
                'name' => 'Email Campaign', 'slug' => 'email-campaign', 'category' => 'email', 'type' => 'text',
                'description' => 'Write compelling email campaigns that convert',
                'icon' => 'mail',
                'system_prompt' => 'You are an email marketing expert. Write compelling emails with strong subject lines, clear CTAs, and persuasive copy.',
                'user_prompt_template' => 'Write a {{type}} email about "{{topic}}". Goal: {{goal}}. Subject line + body.',
                'fields' => [
                    ['name' => 'topic', 'label' => 'Email Topic', 'type' => 'text', 'placeholder' => 'e.g., Summer sale announcement', 'required' => true],
                    ['name' => 'type', 'label' => 'Email Type', 'type' => 'select', 'options' => ['Newsletter', 'Promotional', 'Welcome', 'Follow-up', 'Re-engagement'], 'default' => 'Promotional'],
                    ['name' => 'goal', 'label' => 'Goal', 'type' => 'select', 'options' => ['Click-through', 'Purchase', 'Sign up', 'Inform'], 'default' => 'Click-through'],
                ],
            ],
            [
                'name' => 'Product Description', 'slug' => 'product-description', 'category' => 'ecommerce', 'type' => 'text',
                'description' => 'Create persuasive product descriptions that sell',
                'icon' => 'shopping-bag',
                'system_prompt' => 'You are an e-commerce copywriter. Write persuasive product descriptions that highlight benefits, features, and create urgency.',
                'user_prompt_template' => 'Write a product description for "{{product}}". Key features: {{features}}. Target buyer: {{buyer}}.',
                'fields' => [
                    ['name' => 'product', 'label' => 'Product Name', 'type' => 'text', 'required' => true],
                    ['name' => 'features', 'label' => 'Key Features', 'type' => 'textarea', 'placeholder' => 'List the main features...', 'rows' => 3],
                    ['name' => 'buyer', 'label' => 'Target Buyer', 'type' => 'text', 'placeholder' => 'e.g., Tech-savvy professionals'],
                ],
            ],
            [
                'name' => 'SEO Meta Tags', 'slug' => 'seo-meta', 'category' => 'seo', 'type' => 'text',
                'description' => 'Generate optimized meta titles and descriptions',
                'icon' => 'search',
                'system_prompt' => 'You are an SEO specialist. Generate optimized meta titles (60 chars max) and descriptions (160 chars max) with target keywords naturally integrated.',
                'user_prompt_template' => 'Generate SEO meta title and description for a page about "{{topic}}". Primary keyword: {{keyword}}. Page type: {{page_type}}.',
                'fields' => [
                    ['name' => 'topic', 'label' => 'Page Topic', 'type' => 'text', 'required' => true],
                    ['name' => 'keyword', 'label' => 'Primary Keyword', 'type' => 'text', 'required' => true],
                    ['name' => 'page_type', 'label' => 'Page Type', 'type' => 'select', 'options' => ['Homepage', 'Product page', 'Blog post', 'Service page', 'Landing page']],
                ],
            ],
            [
                'name' => 'Ad Copy Generator', 'slug' => 'ad-copy', 'category' => 'marketing', 'type' => 'text',
                'description' => 'Create high-converting ad copy for Google, Facebook, etc.',
                'icon' => 'megaphone',
                'system_prompt' => 'You are a performance marketing expert. Write compelling ad copy with strong headlines, clear value propositions, and effective CTAs.',
                'user_prompt_template' => 'Write {{platform}} ad copy for "{{product}}". USP: {{usp}}. Target: {{target}}. Generate 3 variations.',
                'fields' => [
                    ['name' => 'product', 'label' => 'Product/Service', 'type' => 'text', 'required' => true],
                    ['name' => 'platform', 'label' => 'Ad Platform', 'type' => 'select', 'options' => ['Google Ads', 'Facebook/Instagram', 'LinkedIn', 'Twitter/X']],
                    ['name' => 'usp', 'label' => 'Unique Selling Point', 'type' => 'text'],
                    ['name' => 'target', 'label' => 'Target Audience', 'type' => 'text'],
                ],
            ],
            [
                'name' => 'Laravel API Generator', 'slug' => 'laravel-api', 'category' => 'code', 'type' => 'code',
                'description' => 'Generate Laravel REST API code with models, migrations, and controllers',
                'icon' => 'code',
                'system_prompt' => 'You are a senior Laravel developer. Generate clean, well-structured Laravel code following best practices. Include migration, model, controller, and routes.',
                'user_prompt_template' => 'Generate a complete Laravel REST API for a {{resource}} resource with these fields: {{fields}}. Include: migration, model with relationships, resource controller, form request, and API routes.',
                'fields' => [
                    ['name' => 'resource', 'label' => 'Resource Name', 'type' => 'text', 'placeholder' => 'e.g., Product', 'required' => true],
                    ['name' => 'fields', 'label' => 'Fields', 'type' => 'textarea', 'placeholder' => 'name: string\nprice: decimal\ncategory_id: foreign\nis_active: boolean', 'rows' => 5],
                ],
            ],
            [
                'name' => 'AI Image Creator', 'slug' => 'ai-image', 'category' => 'creative', 'type' => 'image',
                'description' => 'Create stunning AI-generated images from descriptions',
                'icon' => 'image',
                'system_prompt' => '',
                'user_prompt_template' => '{{style}} style image of {{description}}. {{mood}} mood, {{detail}}.',
                'fields' => [
                    ['name' => 'description', 'label' => 'Image Description', 'type' => 'textarea', 'placeholder' => 'A futuristic city skyline at sunset...', 'required' => true, 'rows' => 3],
                    ['name' => 'style', 'label' => 'Art Style', 'type' => 'select', 'options' => ['Photorealistic', 'Digital art', 'Oil painting', 'Watercolor', 'Minimalist', '3D render', 'Anime', 'Pixel art']],
                    ['name' => 'mood', 'label' => 'Mood', 'type' => 'select', 'options' => ['Dramatic', 'Peaceful', 'Energetic', 'Mysterious', 'Warm', 'Cool']],
                    ['name' => 'detail', 'label' => 'Additional Details', 'type' => 'text', 'placeholder' => 'e.g., high detail, cinematic lighting'],
                ],
            ],
        ];

        foreach ($templates as $tmpl) {
            Template::create($tmpl);
        }
    }
}
