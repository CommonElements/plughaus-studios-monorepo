<?php
/**
 * Template Name: Blog Page
 * Professional blog page
 */

get_header(); ?>

<main id="primary" class="site-main">
    
    <!-- Page Header -->
    <section class="hero-section" style="padding: 4rem 0;">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="hero-content" style="grid-template-columns: 1fr; text-align: center;">
                <div class="hero-text">
                    <h1 class="hero-title" style="font-size: 3rem;">
                        Vireo <span class="text-gradient">Blog</span>
                    </h1>
                    <p class="hero-description">
                        Stay updated with the latest WordPress tips, plugin tutorials, and business insights.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Post -->
    <section style="padding: 6rem 0; background: var(--gray-50);">
        <div class="container">
            <div style="background: white; border-radius: 12px; padding: 3rem; margin-bottom: 4rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;">
                    <div>
                        <div style="display: inline-block; background: var(--primary-color); color: white; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.875rem; font-weight: 600; margin-bottom: 1.5rem;">Featured Article</div>
                        <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem; color: var(--gray-900);">Building Your First Property Management Workflow</h2>
                        <p style="font-size: 1.125rem; color: var(--gray-600); margin-bottom: 2rem; line-height: 1.6;">
                            Learn how to set up efficient property management workflows that save time and improve tenant satisfaction. Complete with templates and best practices.
                        </p>
                        
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; color: var(--gray-500); font-size: 0.95rem;">
                            <span><i class="fas fa-calendar"></i> December 15, 2024</span>
                            <span><i class="fas fa-clock"></i> 8 min read</span>
                            <span><i class="fas fa-tag"></i> Tutorials</span>
                        </div>
                        
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i>
                            Read Article
                        </a>
                    </div>
                    
                    <div style="text-align: center;">
                        <div style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); border-radius: 12px; padding: 3rem; color: white;">
                            <i class="fas fa-cogs" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.9;"></i>
                            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Workflow Guide</h3>
                            <p style="opacity: 0.9;">Step-by-step tutorial series</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blog Categories -->
            <div style="display: flex; gap: 1rem; margin-bottom: 3rem; flex-wrap: wrap; justify-content: center;">
                <a href="#" style="background: var(--primary-color); color: white; padding: 0.75rem 1.5rem; border-radius: 50px; text-decoration: none; font-weight: 600;">All Posts</a>
                <a href="#" style="background: white; color: var(--gray-700); padding: 0.75rem 1.5rem; border-radius: 50px; text-decoration: none; border: 2px solid var(--gray-200);">Tutorials</a>
                <a href="#" style="background: white; color: var(--gray-700); padding: 0.75rem 1.5rem; border-radius: 50px; text-decoration: none; border: 2px solid var(--gray-200);">Product Updates</a>
                <a href="#" style="background: white; color: var(--gray-700); padding: 0.75rem 1.5rem; border-radius: 50px; text-decoration: none; border: 2px solid var(--gray-200);">Business Tips</a>
                <a href="#" style="background: white; color: var(--gray-700); padding: 0.75rem 1.5rem; border-radius: 50px; text-decoration: none; border: 2px solid var(--gray-200);">WordPress</a>
            </div>

            <!-- Blog Posts Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem;">
                
                <!-- Post 1 -->
                <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                    <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative;">
                        <div style="position: absolute; bottom: 1rem; left: 1rem; background: white; color: var(--primary-color); padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.875rem; font-weight: 600;">Tutorial</div>
                    </div>
                    <div style="padding: 2rem;">
                        <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--gray-900);">
                            <a href="#" style="text-decoration: none; color: inherit;">10 WordPress Security Best Practices for Property Managers</a>
                        </h3>
                        <p style="color: var(--gray-600); margin-bottom: 1.5rem; line-height: 1.6;">
                            Protect your property management data with these essential WordPress security measures.
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--gray-500); font-size: 0.875rem;">
                            <span><i class="fas fa-calendar"></i> Dec 12, 2024</span>
                            <span><i class="fas fa-clock"></i> 6 min read</span>
                        </div>
                    </div>
                </article>

                <!-- Post 2 -->
                <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                    <div style="height: 200px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); position: relative;">
                        <div style="position: absolute; bottom: 1rem; left: 1rem; background: white; color: var(--secondary-color); padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.875rem; font-weight: 600;">Product Update</div>
                    </div>
                    <div style="padding: 2rem;">
                        <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--gray-900);">
                            <a href="#" style="text-decoration: none; color: inherit;">Property Management Pro v2.1 - New Analytics Dashboard</a>
                        </h3>
                        <p style="color: var(--gray-600); margin-bottom: 1.5rem; line-height: 1.6;">
                            We've added powerful new analytics features to help you track your property performance.
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--gray-500); font-size: 0.875rem;">
                            <span><i class="fas fa-calendar"></i> Dec 10, 2024</span>
                            <span><i class="fas fa-clock"></i> 4 min read</span>
                        </div>
                    </div>
                </article>

                <!-- Post 3 -->
                <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                    <div style="height: 200px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); position: relative;">
                        <div style="position: absolute; bottom: 1rem; left: 1rem; background: white; color: var(--success-color); padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.875rem; font-weight: 600;">Business Tips</div>
                    </div>
                    <div style="padding: 2rem;">
                        <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--gray-900);">
                            <a href="#" style="text-decoration: none; color: inherit;">How to Scale Your Property Management Business</a>
                        </h3>
                        <p style="color: var(--gray-600); margin-bottom: 1.5rem; line-height: 1.6;">
                            Proven strategies for growing from 10 to 100+ properties without losing your sanity.
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--gray-500); font-size: 0.875rem;">
                            <span><i class="fas fa-calendar"></i> Dec 8, 2024</span>
                            <span><i class="fas fa-clock"></i> 10 min read</span>
                        </div>
                    </div>
                </article>

                <!-- Post 4 -->
                <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                    <div style="height: 200px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); position: relative;">
                        <div style="position: absolute; bottom: 1rem; left: 1rem; background: white; color: var(--primary-color); padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.875rem; font-weight: 600;">WordPress</div>
                    </div>
                    <div style="padding: 2rem;">
                        <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--gray-900);">
                            <a href="#" style="text-decoration: none; color: inherit;">WordPress Performance Optimization for Business Sites</a>
                        </h3>
                        <p style="color: var(--gray-600); margin-bottom: 1.5rem; line-height: 1.6;">
                            Speed up your WordPress site with these proven optimization techniques.
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--gray-500); font-size: 0.875rem;">
                            <span><i class="fas fa-calendar"></i> Dec 5, 2024</span>
                            <span><i class="fas fa-clock"></i> 7 min read</span>
                        </div>
                    </div>
                </article>

                <!-- Post 5 -->
                <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                    <div style="height: 200px; background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); position: relative;">
                        <div style="position: absolute; bottom: 1rem; left: 1rem; background: white; color: var(--secondary-color); padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.875rem; font-weight: 600;">Tutorial</div>
                    </div>
                    <div style="padding: 2rem;">
                        <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--gray-900);">
                            <a href="#" style="text-decoration: none; color: inherit;">Setting Up Automated Rent Collection</a>
                        </h3>
                        <p style="color: var(--gray-600); margin-bottom: 1.5rem; line-height: 1.6;">
                            Learn how to automate your rent collection process and reduce late payments.
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--gray-500); font-size: 0.875rem;">
                            <span><i class="fas fa-calendar"></i> Dec 3, 2024</span>
                            <span><i class="fas fa-clock"></i> 5 min read</span>
                        </div>
                    </div>
                </article>

                <!-- Post 6 -->
                <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                    <div style="height: 200px; background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); position: relative;">
                        <div style="position: absolute; bottom: 1rem; left: 1rem; background: white; color: var(--success-color); padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.875rem; font-weight: 600;">Business Tips</div>
                    </div>
                    <div style="padding: 2rem;">
                        <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--gray-900);">
                            <a href="#" style="text-decoration: none; color: inherit;">Tenant Screening: The Complete Guide</a>
                        </h3>
                        <p style="color: var(--gray-600); margin-bottom: 1.5rem; line-height: 1.6;">
                            Everything you need to know about finding and screening quality tenants.
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--gray-500); font-size: 0.875rem;">
                            <span><i class="fas fa-calendar"></i> Dec 1, 2024</span>
                            <span><i class="fas fa-clock"></i> 12 min read</span>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Load More -->
            <div style="text-align: center; margin-top: 4rem;">
                <button class="btn btn-outline btn-xl">
                    <i class="fas fa-plus"></i>
                    Load More Posts
                </button>
            </div>
        </div>
    </section>

    <!-- Newsletter Signup -->
    <section style="padding: 6rem 0; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); color: white;">
        <div class="container">
            <div style="max-width: 600px; margin: 0 auto; text-align: center;">
                <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">Stay in the Loop</h2>
                <p style="font-size: 1.125rem; margin-bottom: 2rem; opacity: 0.9;">
                    Get the latest tutorials, tips, and product updates delivered to your inbox.
                </p>
                <form style="display: flex; gap: 1rem; max-width: 400px; margin: 0 auto;">
                    <input type="email" placeholder="Enter your email" style="flex: 1; padding: 1rem; border: none; border-radius: 8px; font-size: 1rem;">
                    <button type="submit" class="btn btn-secondary" style="white-space: nowrap;">
                        <i class="fas fa-paper-plane"></i>
                        Subscribe
                    </button>
                </form>
                <p style="font-size: 0.875rem; margin-top: 1rem; opacity: 0.8;">
                    No spam, unsubscribe anytime.
                </p>
            </div>
        </div>
    </section>

</main>

<style>
@media (max-width: 768px) {
    .container > div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
    }
    
    .container > div[style*="grid-template-columns: repeat(auto-fit, minmax(350px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
    
    form[style*="display: flex"] {
        flex-direction: column !important;
    }
}
</style>

<?php get_footer(); ?>