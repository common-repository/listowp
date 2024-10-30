<?php

class Listo_Config_Upgrade {
	public static function dashboard() {
		wp_enqueue_style('listowp-admin-upgrade');

		?>
      <div class="loa-wrapper loa-upgrade-wrapper">
				<div class="loa-upgrade-inner">
					<!-- UPGRADE INTRO -->
					<div class="loa-upgrade-intro">
						<h2>Get ListoWP Pro</h2>
						<p>Receive hands-on technical support.</p>
						<p>Use all Pro features including third party integrations.</p>
						<p>Get all new features & improvements the moment they are released.</p>
					</div>
					<!-- UPGRADE PRICING -->
					<div class="loa-upgrade-pricing">
						<div class="loa-upgrade-pricing-inner">
							<div class="loa-upgrade-pricing__column loa-upgrade-pricing__column--yearly">
								<div class="loa-upgrade-pricing__title">Yearly <span>20% off!</span></div>
								<div class="loa-upgrade-pricing__price"><strike>$29</strike> $23</div>
								<div class="loa-upgrade-pricing__action">
									<a href="https://listowp.com/?listo_add_to_cart=yearly&discount=UPGRADE20" target="_blank">BUY NOW</a>
								</div>
								<div class="loa-upgrade-pricing__price__discount">Use code <code>UPGRADE20</code> on checkout</div>
								<div class="loa-upgrade-pricing__features">
									<div class="loa-upgrade-pricing__feature">
										<i class="fa-solid fa-check"></i>All features, current & future
									</div>
									<div class="loa-upgrade-pricing__feature">
										<i class="fa-solid fa-check"></i>One year of technical support
									</div>
									<div class="loa-upgrade-pricing__feature">
										<i class="fa-solid fa-check"></i>Up to <strong>10 sites</strong>
									</div>
									<div class="loa-upgrade-pricing__feature">
										<i class="fa-solid fa-check"></i>Auto-renews, cancel anytime
									</div>
								</div>
							</div>
							<div class="loa-upgrade-pricing__column loa-upgrade-pricing__column--lifetime">
								<div class="loa-upgrade-pricing__title">Lifetime <span>20% off!</span></div>
								<div class="loa-upgrade-pricing__price"><strike>$299</strike> $239</div>
								<div class="loa-upgrade-pricing__action">
									<a href="https://listowp.com/?listo_add_to_cart=lifetime&discount=UPGRADE20" target="_blank">BUY NOW</a>
								</div>
								<div class="loa-upgrade-pricing__price__discount">Use code <code>UPGRADE20</code> on checkout</div>
								<div class="loa-upgrade-pricing__features">
									<div class="loa-upgrade-pricing__feature">
										<i class="fa-solid fa-check"></i>All features, current & future
									</div>
									<div class="loa-upgrade-pricing__feature">
										<i class="fa-solid fa-check"></i>One year of technical support
									</div>
									<div class="loa-upgrade-pricing__feature">
										<i class="fa-solid fa-check"></i>Up to <strong>100 sites</strong>
									</div>
									<div class="loa-upgrade-pricing__feature">
										<i class="fa-solid fa-check"></i>One time payment
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- UPGRADE GUARANTEE -->
					<div class="loa-upgrade-guarantee">
						<h3>Unconditional 30 Day Money-Back Guarantee!</h3>
						<p>If you’d like to cancel your transaction, let us know within 30 calendar days of purchase or renewal.</p>
						<p>We will give you a full refund, no fuss. That’s a promise!</p>
					</div>
					<!-- UPGRADE FAQ -->
					<div class="loa-upgrade-faq">
						<div class="loa-upgrade-faq-inner">
							<div class="loa-upgrade-faq__column">
								<div class="loa-upgrade-faq__title">What if I don’t like it?</div>
								<div class="loa-upgrade-faq__desc">Our 30 Day Money Back Guarantee has your back! Any purchase or renewal is refundable as long as the request comes 30 calendar days from the transaction.</div>
							</div>
							<div class="loa-upgrade-faq__column">
								<div class="loa-upgrade-faq__title">What is the license key for?</div>
								<div class="loa-upgrade-faq__desc">The license key allows your site to connect to ours in order to request automatic updates.<br> You also need an active license key to receive technical support.</div>
							</div>
							<div class="loa-upgrade-faq__column">
								<div class="loa-upgrade-faq__title">How long will I receive support?</div>
								<div class="loa-upgrade-faq__desc">For subscription-based plans, the support is provided as long as your license is active.<br> Lifetime plan receives support for a year, counting from the date of the purchase.</div>
							</div>
							<div class="loa-upgrade-faq__column">
								<div class="loa-upgrade-faq__title">Is subscription necessary?</div>
								<div class="loa-upgrade-faq__desc">Any subscription-based plan can be cancelled anytime. But if you let your license expire, you will lose access to updates and support.<br> You can opt for a Lifetime plan, which doesn’t have a subscription. </div>
							</div>
							<div class="loa-upgrade-faq__column">
								<div class="loa-upgrade-faq__title">What if my license expires?</div>
								<div class="loa-upgrade-faq__desc">You can still use ListoWP Pro with an expired license, but you will lose access to the Pro services: updates & support. That includes security and compatibility patches.<br> Lifetime plan license never expires. </div>
							</div>
							<div class="loa-upgrade-faq__column">
								<div class="loa-upgrade-faq__title">I have more questions</div>
								<div class="loa-upgrade-faq__desc">We are always happy to help!<br>	If you have any questions, suggestions and/or comments, please do not hesitate to contact us. We will get back to you as soon as we can.
									<a href="#" target="_blank">Contact & Support</a></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
}