<template>
  <div class="platform-page inner-pages filter-header">
	   <header>
     		<ThePrimaryHeader v-if="isShownComponent"></ThePrimaryHeader>
            <TheSecondaryHeader :search="search" v-if="isShownComponent"></TheSecondaryHeader>
    	</header>
      	<main>
			<b-container>
		  	<div class="slider-banner-block">
					<b-row>
						<b-col lg="6" class="slider-col">
							<!-- <div class="content-loader-wrap slider-loader">
						      <div class="content-loader"></div>
						    </div> -->
							<div class="thumb-slider">
								<div class="gallery-top">
									<div class="img-wrap inner-gallery-block">
										<img src="../assets/images/gallery-img03.jpg">
									</div>
									<div class="video-wrap inner-gallery-block">
										 <iframe id="video" width="560" height="315" src='https://www.youtube.com/embed/YE7VzlLtp-4' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> 
									</div>
									
								</div>

								<carousel  :nav="true" :dots="false" :items="5" :loop="true" :mouseDrag="false" :touchDrag="false" class="gallery-thumbs" :margin ="8" :responsive="{0:{items:3},576:{items:4},1200:{items:5}}">
									<div class="img-block thumbs-col">
										<img src="../assets/images/gallery-img01.jpg">
									</div>
									<div class="img-block thumbs-col">
										<img src="../assets/images/gallery-img02.jpg">
									</div>
									<div class="img-block thumbs-col">
										<img src="../assets/images/gallery-img03.jpg">
									</div>
									<div class="img-block thumbs-col">
										<img src="../assets/images/gallery-img04.jpg">
									</div>
									<div class="img-block thumbs-col">
										<img src="../assets/images/gallery-img05.jpg">
									</div>
									<div class="video-block thumbs-col">
										<img src="http://i3.ytimg.com/vi/YE7VzlLtp-4/hqdefault.jpg" data-src="https://www.youtube.com/embed/YE7VzlLtp-4" class="video-item">	
										<!-- <img src="http://i3.ytimg.com/vi/_gJyJ8NvZgg/maxresdefault.jpg" data-src="https://www.youtube.com/embed/YE7VzlLtp-4" class="video-item">	 -->
										<i class="btn-play"></i>
									</div>
								</carousel>
							</div>
						</b-col>
						<b-col lg="6" class="ml-auto banner-content-wrap">
							<div class="banner-content-block">
							<h1>CSR initiative stands for Coffee &amp;
								Farmer Equity</h1>
								<div class="rating-with-btn">
									<div class="rating-block">
										<star-rating
										v-bind:increment="0.5"
										v-bind:max-rating="5"
										:border-width="2"
										border-color="#757575"
										inactive-color="#Fff"
										active-color="#F7D341"
										v-bind:star-size="25"
										:rating="rating"
										@rating-selected ="setRating"
										>
										</star-rating>
									</div>
									<b-button 
 									v-bind:class="{ 
	 									'btn-borderprimary': true, 
	 									'icon-btn': true,
	 									'added-fav' : missionAddedToFavoriteByUser
 									}"
									 @click="favoriteMission(missionId)">
									<i class="normal-img">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 21" width="24" height="21">
                                        <g id="Main Content">
                                            <g id="1">
                                                <g id="Image content">
                                                    <path id="Forma 1" d="M22.1 2.86C20.9 1.66 19.3 1 17.59 1C15.89 1 14.29 1.66 13.08 2.86L12.49 3.45L11.89 2.86C10.69 1.66 9.08 1 7.38 1C5.67 1 4.07 1.66 2.87 2.86C0.38 5.34 0.38 9.36 2.87 11.84L11.78 20.71C11.93 20.86 12.11 20.95 12.3 20.98C12.36 20.99 12.43 21 12.49 21C12.74 21 13 20.9 13.19 20.71L22.1 11.84C24.59 9.36 24.59 5.34 22.1 2.86ZM20.71 10.45L12.49 18.64L4.26 10.45C2.54 8.74 2.54 5.96 4.26 4.25C5.09 3.42 6.2 2.96 7.38 2.96C8.56 2.96 9.66 3.42 10.5 4.25L11.79 5.53C12.16 5.9 12.81 5.9 13.18 5.53L14.47 4.25C15.31 3.42 16.41 2.96 17.59 2.96C18.77 2.96 19.88 3.42 20.71 4.25C22.43 5.96 22.43 8.74 20.71 10.45Z" />
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    </i>
                                     <i class="hover-img">
                                       <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 492.7 426.8" style="enable-background:new 0 0 492.7 426.8;" xml:space="preserve">
                                        <g>
                                            <g id="Icons_18_">
                                                <path d="M492.7,133.1C492.7,59.6,433.1,0,359.7,0c-48,0-89.9,25.5-113.3,63.6C222.9,25.5,181,0,133,0
                                                    C59.6,0,0,59.6,0,133.1c0,40,17.7,75.8,45.7,100.2l188.5,188.6c3.2,3.2,7.6,5,12.1,5s8.9-1.8,12.1-5L447,233.2
                                                    C475,208.9,492.7,173.1,492.7,133.1z"/>
                                            </g>
                                        </g>
                                        </svg>
                                    </i>	
									<span v-if="missionAddedToFavoriteByUser">
										{{ $t('label.remove_from_favourite') }}
									</span>

									<span v-else>
										{{ $t('label.add_to_favourite') }}
									</span>

								</b-button>
								</div>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>
							<div class="group-details">
								<div class="top-strip">
									<span>Plant 10,000 Trees</span>
								</div>
								<div class="group-details-inner has-progress">
									<div class="detail-column info-block">
										<i class="icon-wrap">
											<img src="../assets/images/user-icon.svg" alt="user">
										</i>
										<div class="text-wrap">
											<span class="title-text mb-1">10</span>
											<span class="subtitle-text">Seats left</span>
										</div>
									</div>
									<div class="detail-column progress-block">
										<i class="icon-wrap">
											<img src="../assets/images/target-ic.svg" alt="user">
										</i>
										<div class="text-wrap">
											<b-progress :value="value" :max="max" class="mb-2"></b-progress>
											<span class="subtitle-text">8000 achieved</span>
										</div>
									</div>
								</div>
							</div>
							<b-list-group class="info-box">
								<b-list-group-item>
									<div>
										<i class="img-wrap">
											<img src="../assets/images/location-black.svg" alt="" />
										</i>
										<span class="label">{{ $t("label.city")}}</span>
										<p class="text-wrap">London</p>
									</div>
								</b-list-group-item>
								<b-list-group-item>
									<div>
										<i class="img-wrap">
											<img src="../assets/images/earth-ic.svg" alt="" />
										</i>
										<span class="label">{{ $t("label.theme")}}</span>
										<p class="text-wrap">Environment</p>
									</div>
								</b-list-group-item>
								<b-list-group-item>
									<div>
										<i class="img-wrap">
											<img src="../assets/images/calendar.svg" alt="" />
										</i>
										<span class="label">{{ $t("label.date")}}</span>
										<p class="text-wrap">Ongoing Opportunity</p>
									</div>
								</b-list-group-item>
								<b-list-group-item>
									<div>
										<i class="img-wrap">
											<img src="../assets/images/group-ic.svg" alt="" />
										</i>
										<span class="label">{{ $t("label.organisation")}}</span>
										<p class="text-wrap">CSE Network</p>
									</div>
								</b-list-group-item>
							</b-list-group>
							<div class="btn-wrap">
								<b-button class="btn-borderprimary icon-btn"  
								@click="handleModal(missionId)">
									<i>
										<svg height="512pt" viewBox="0 0 512 512" width="512pt"
											xmlns="http://www.w3.org/2000/svg">
											<path
												d="m512 428h-84v84h-40v-84h-84v-40h84v-84h40v84h84zm-212.695312-204.5625c1.757812 7.910156 2.695312 16.128906 2.695312 24.5625 0 34.550781-15.59375 65.527344-40.105469 86.269531.699219.277344 1.40625.546875 2.105469.832031v44.199219c-21.414062-11.667969-45.945312-18.300781-72-18.300781v-.039062c-.332031.007812-.667969.007812-1 .015624v.023438c-83.261719 0-151 67.738281-151 151h-40c0-79.371094 48.671875-147.582031 117.730469-176.378906-25.449219-20.734375-41.730469-52.3125-41.730469-87.621094 0-62.308594 50.691406-113 113-113 7.40625 0 14.644531.722656 21.65625 2.089844-1.734375-7.84375-2.65625-15.988282-2.65625-24.34375 0-62.167969 50.578125-112.746094 112.746094-112.746094 62.167968 0 112.746094 50.578125 112.746094 112.746094 0 34.894531-15.9375 66.136718-40.910157 86.832031 33.011719 13.109375 61.464844 35.117187 82.304688 63.421875h-53.847657c-24.847656-22.023438-56.976562-36-92.273437-37.796875-2.652344.1875-5.324219.289063-8.019531.289063-7.332032 0-14.5-.710938-21.441406-2.054688zm-51.304688-110.691406c0 40.113281 32.632812 72.746094 72.746094 72.746094 40.109375 0 72.746094-32.632813 72.746094-72.746094 0-40.113282-32.636719-72.746094-72.746094-72.746094-40.113282 0-72.746094 32.632812-72.746094 72.746094zm14 135.253906c0-40.253906-32.746094-73-73-73s-73 32.746094-73 73 32.746094 73 73 73 73-32.746094 73-73zm0 0" />
										</svg>
									</i>
									<span>{{ $t("label.recommend_to_co_worker") }}</span>
								</b-button>
								<b-button class="btn-bordersecondary icon-btn">
										<span>{{ $t("label.apply_now") }}</span>
										<i>
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 16" width="19" height="15">
												<g id="Main Content">
													<g id="1">
														<g id="Button">
															<path id="Forma 1 copy 12" class="shp0"
																d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z" />
														</g>
													</g>
												</g>
											</svg>
										</i>
									</b-button>
							</div>
								</div>
						</b-col>
					</b-row> 	
		  		</div>
		  <div class="platform-details-wrap">
          <b-row>
              <b-col xl="8" lg="7" class="platform-details-left">
				 <div class="platform-details-tab tabs">
					 <ul class="nav-tabs nav">
						 <li><a href="javascript:void(0)" data-id="mission" class="tablinks active">
						 {{ $t("label.mission") }}</a></li>
						 <li><a href="javascript:void(0)" data-id="organization" class="tablinks">
						 {{ $t("label.organisation") }}</a></li>
						 <li><a href="javascript:void(0)" data-id="sponsored" class="tablinks">
						 {{ $t("label.sponsored") }}</a></li>
						 <li><a href="javascript:void(0)" data-id="comments" class="tablinks">
						 {{ $t("label.comments") }}</a></li>
					 </ul>
					 <div class="tab-content-wrap">
						<div class="tabs">
							<div class="tab-title">
								<h3 v-b-toggle.mission>Mission</h3>
							</div>
								<b-collapse id="mission" visible accordion="my-accordion" role="tabpanel" class="tab-content">
									<h2>Introduction</h2>
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
									<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
									<h2>Challenge</h2>
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
									<h2>{{ $t("label.documents") }}</h2>
									<!-- <div class="document-list-wrap">
										<b-link href="#" target="_blank" title=""><CustomChip :textVal="'lorem-ipsum.pdf'" class="has-img no-close" :url="bgImage[0]"/></b-link>
										<b-link href="#" target="_blank" title=""><CustomChip :textVal="'at_vero_eos_accusamus.doc'"  class="has-img no-close" :url="bgImage[1]"/></b-link>
										<b-link href="#" target="_blank" title=""><CustomChip :textVal="'important_doc.xls'"  class="has-img no-close" :url="bgImage[2]"/></b-link>
										<b-link href="#" target="_blank" title=""><CustomChip :textVal="'loremm.pdf'"  class="has-img no-close" :url="bgImage[0]"/></b-link>
										<b-link href="#" target="_blank" title=""><CustomChip :textVal="'important_doc.xls'"  class="has-img no-close" :url="bgImage[2]"/></b-link>
										<b-link href="#" target="_blank" title=""><CustomChip :textVal="'lorem-ipsum.pdf'"  class="has-img no-close" :url="bgImage[0]"/></b-link>
									</div>     -->                    
								</b-collapse>
					 		</div>
							<div class="tabs">
								<div class="tab-title">
									<h3 v-b-toggle.organization>{{ $t("label.organisation") }}</h3>
								</div>
									<b-collapse id="organization" accordion="my-accordion" role="tabpanel" class="tab-content">
										<b-media class="org-media">
											<div class="img-wrap" slot="aside">
												<img :src="orgLogo" alt="" />
											</div>
											<h3>CES Industrial Solutions Network</h3>
											<p><i> <img src="../assets/images/user-icon1.svg" alt="" /></i>Melvin A. Sawyer, Managing Director</p>
										</b-media>
										<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco... <b-link class="more-link">See more</b-link></p>
										<b-list-group class="contact-info-list">
											<b-list-group-item>
												<b-link>
												<i class="left-icon">
													<img src="../assets/images/location-black.svg" alt="" />
												</i>
												<p class="text-wrap">4456 Illinois Avenue <span>Oregon City, OR 97045</span></p>
												</b-link>
											</b-list-group-item>
											<b-list-group-item>
												<b-link>
												<i class="left-icon">
													<img src="../assets/images/phone-ic.svg" alt="" />
												</i>
												<p class="text-wrap">+1 503-742-7810</p>
												</b-link>
											</b-list-group-item>
											<b-list-group-item>
												<b-link>
												<i class="left-icon">
													<img src="../assets/images/mailbox-ic.svg" alt="" />
												</i>
												<p class="text-wrap">melvinasawyer@yahoo.com</p>
												</b-link>
											</b-list-group-item>
											<b-list-group-item>
												<b-link>
												<i class="left-icon">
													<img src="../assets/images/earth-ic.svg" alt="" />
												</i>
												<p class="text-wrap">www.cesnetworksolution.com</p>
												</b-link>
											</b-list-group-item>
										</b-list-group>
										<h2>About Organization</h2>
										<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
										</p>
										<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
									</b-collapse>
							</div>
							<div class="tabs">
								<div class="tab-title">
									<h3 v-b-toggle.sponsored>{{ $t("label.sponsored") }}</h3>
								</div>
									<b-collapse id="sponsored" accordion="my-accordion" role="tabpanel" class="tab-content">
										<h2>Introduction</h2>
										<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
										<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
										<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
										<h2>How do we support this mission</h2>
										<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
										<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
										<h2>Documents</h2>
										<!-- <div class="document-list-wrap">
											<b-link href="#" target="_blank" title=""><CustomChip :textVal="'lorem-ipsum.pdf'" class="has-img no-close" :url="bgImage[0]"/></b-link>
											<b-link href="#" target="_blank" title=""><CustomChip :textVal="'at_vero_eos_accusamus.doc'"  class="has-img no-close" :url="bgImage[1]"/></b-link>
										</div>   -->  
									</b-collapse>
							</div>
							<div class="tabs">
								<div class="tab-title">
									<h3 v-b-toggle.comments>{{ $t("label.comment") }}</h3>
								</div>
								<div>
									<b-collapse id="comments" accordion="my-accordion" role="tabpanel" class="tab-content comment-block">
										<b-form class="comment-form">
									<label>{{ $t("label.comment") }}</label>
									<b-form-textarea id="" 
									:placeholder="$t('placeholder.comment')"
									placeholder="Enter your comments..." rows="4" size="lg"  no-resize></b-form-textarea>
									<b-button class="btn-bordersecondary">{{ $t("label.post_comment") }}</b-button>
								</b-form>
								<div class="comment-list">
									<div class="more-inner-list">
										<div class="comment-list-item">
											<b-media class="comment-media">
												<i slot="aside" class="user-profile-icon" :style="{backgroundImage: 'url(' + commentImg[0] + ')'}"></i>
												<h5>Arnold Hubler</h5>
												<p>Monday, June 10, 2019, 10:30PM</p>
											</b-media>
											<p>
												Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
											</p>
										</div>
										<div class="more-inner-list">
											<div class="comment-list-item inner-list-item">
												<b-media class="comment-media">
													<i slot="aside" class="user-profile-icon" :style="{backgroundImage: 'url(' + commentImg[1] + ')'}"></i>
													<h5>Herbert Brown</h5>
													<p>Monday, June 10, 2019, 10:30PM</p>
												</b-media>
												<p>
													But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth.
												</p>
											</div>
											<div class="more-inner-list">
												<div class="comment-list-item inner-list-item">
													<b-media class="comment-media">
														<i slot="aside" class="user-profile-icon" :style="{backgroundImage: 'url(' + commentImg[0] + ')'}"></i>
														<h5>Arnold Hubler</h5>
														<p>Monday, June 10, 2019, 10:30PM</p>
													</b-media>
													<p>
														Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
													</p>
												</div>
											</div>	
										</div>	
									</div>
									<div class="more-inner-list">
										<div class="comment-list-item inner-list-item">
											<b-media class="comment-media">
												<i slot="aside" class="user-profile-icon" :style="{backgroundImage: 'url(' + commentImg[0] + ')'}"></i>
												<h5>Arnold Hubler</h5>
												<p>Monday, June 10, 2019, 10:30PM</p>
											</b-media>
											<p>
												Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
											</p>
										</div>
									</div>	
									<div class="more-inner-list">
										<div class="comment-list-item inner-list-item">
											<b-media class="comment-media">
												<i slot="aside" class="user-profile-icon" :style="{backgroundImage: 'url(' + commentImg[0] + ')'}"></i>
												<h5>Arnold Hubler</h5>
												<p>Monday, June 10, 2019, 10:30PM</p>
											</b-media>
											<p>
												But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth.
											</p>
										</div>
									</div>	
								</div>
								<div class="more-comment-list">
									<b-link href="#" target="_blank" title="Read More Comments" class="border-hover-link">{{ $t("label.read_more_comment") }}</b-link>
								</div>
									</b-collapse>
								</div>
							</div>
					 </div>	
  				</div>
                </b-col>
                <b-col xl="4" lg="5" class="platform-details-right">
					<div class="info-block">
						 <h2 class="title-with-border"><span>{{ $t("label.information") }}</span></h2>
						<div class="table-wrap">
							<div class="table-row">
								<span class="label-col">Skills</span>
								<span class="detail-col">Cool, Easy going, Math, Computer</span>
							</div>
							<div class="table-row">
								<span class="label-col">Days</span>
								<span class="detail-col">Weekend only</span>
							</div>
							<div class="table-row">
								<span class="label-col">Rating</span>
								<span class="detail-col">
									 <star-rating
										:rating="5" :read-only="true" :increment="0.01"
										v-bind:max-rating="5"
										inactive-color="#dddddd"
										active-color="#F7D341"
										v-bind:star-size="23"
										>
									</star-rating>
									<span>(by 125 volunteers)</span>
								</span>
							</div>
						</div>
					</div>
					<div class="recent-volunteer-block">
						<div 
						v-bind:class="{ 
	 									'content-loader-wrap': true, 
	 									'recent-loader': recentVolunterLoader,
 									}"
						>
						      <div class="content-loader"></div>
						    </div>	
						<h2 class="title-with-border"><span>{{ $t("label.recent_volunteers") }} </span></h2>
						<div class="recent-details-block">
							<b-list-group class="volunteers-list"  
								:current-page="currentPage">
								<b-list-group-item v-for="(volunteer , v) in volunteerList" :key="v" >
									<div  class="list-item">
										<i class="user-profile-icon" :style="{backgroundImage: 'url(' + volunteer.avatar + ')'}">
										</i>
										<span>{{volunteer.first_name}} {{volunteer.last_name}}</span>
									</div>
								</b-list-group-item>
							</b-list-group>
							<div class="custom-pagination" v-if="rows > 0">
								<b-pagination
				                    v-model="currentPage"
				                    :total-rows="rows"
				                    :per-page="perPage" 
				                    @change="pageChange" 
			                    >    
			                    </b-pagination>
			     				<span> 
			     				{{((currentPage - 1 ) * perPage ) + 1}} - {{Math.min(perPage * currentPage , rows )}} of {{rows}} {{ $t("label.recent_volunteers") }}</span>
							</div>
						</div>
					</div>
                </b-col>
          </b-row>
	  </div>
      </b-container>
	  <div class="mission-block">
		  <b-container class="card-grid">
			   <h2>Related Missions</h2>
			   <!-- <div class="content-loader-wrap mission-loader">
						      <div class="content-loader"></div>
						    </div> -->
               <b-row>
                <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
                    <b-card no-body>
                        <b-card-header>
                            <div class="header-img-block">
                                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[0]+')'}">
                                    <img src="../assets/images/group-img1.png" alt="group-img">
                                </b-link>
                                <b-link href="#" class="location" title="location">
                                    <i>
                                        <img src="../assets/images/location.svg" alt="location">
                                    </i>London
                                </b-link>
                                <b-button class="favourite-icon" v-b-tooltip.hover title="Add to favourite">
                                    <i class="normal-img">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 21" width="24" height="21">
                                        <g id="Main Content">
                                            <g id="1">
                                                <g id="Image content">
                                                    <path id="Forma 1" d="M22.1 2.86C20.9 1.66 19.3 1 17.59 1C15.89 1 14.29 1.66 13.08 2.86L12.49 3.45L11.89 2.86C10.69 1.66 9.08 1 7.38 1C5.67 1 4.07 1.66 2.87 2.86C0.38 5.34 0.38 9.36 2.87 11.84L11.78 20.71C11.93 20.86 12.11 20.95 12.3 20.98C12.36 20.99 12.43 21 12.49 21C12.74 21 13 20.9 13.19 20.71L22.1 11.84C24.59 9.36 24.59 5.34 22.1 2.86ZM20.71 10.45L12.49 18.64L4.26 10.45C2.54 8.74 2.54 5.96 4.26 4.25C5.09 3.42 6.2 2.96 7.38 2.96C8.56 2.96 9.66 3.42 10.5 4.25L11.79 5.53C12.16 5.9 12.81 5.9 13.18 5.53L14.47 4.25C15.31 3.42 16.41 2.96 17.59 2.96C18.77 2.96 19.88 3.42 20.71 4.25C22.43 5.96 22.43 8.74 20.71 10.45Z" />
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    </i>
                                     <i class="hover-img">
                                       <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 492.7 426.8" style="enable-background:new 0 0 492.7 426.8;" xml:space="preserve">
                                        <g>
                                            <g id="Icons_18_">
                                                <path d="M492.7,133.1C492.7,59.6,433.1,0,359.7,0c-48,0-89.9,25.5-113.3,63.6C222.9,25.5,181,0,133,0
                                                    C59.6,0,0,59.6,0,133.1c0,40,17.7,75.8,45.7,100.2l188.5,188.6c3.2,3.2,7.6,5,12.1,5s8.9-1.8,12.1-5L447,233.2
                                                    C475,208.9,492.7,173.1,492.7,133.1z"/>
                                            </g>
                                        </g>
                                        </svg>
                                    </i>       
                                </b-button>
                                <b-button class="add-icon" title="add">
                                    <img src="../assets/images/add-group-ic.svg" alt="add">
                                </b-button>
                            </div>
							<div class="group-category">
								<span class="category-text">Environment</span>
							</div>
                        </b-card-header>
                        <b-card-body>
                            <div class="content-block">
                                <b-link target="_blank" title="CSR initiative stands" class="card-title mb-2">CSR initiative stands for Coffee and Farmer Equity 
                                </b-link>
                                <b-card-text>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                    incididunt
                                    ut labore...
                                </b-card-text>
                                <div class="group-ratings">
                                    <span class="group-name">CSE Network</span>
                                    <span class="ratings">
                                         <star-rating
										v-bind:increment="0.01"
										v-bind:max-rating="5"
										inactive-color="#dddddd"
										active-color="#F7D341"
										v-bind:star-size="23"
										>
									</star-rating>
                                    </span>
                                </div>
                            </div>
                            <div class="group-details">
                                <div class="top-strip">
                                    <span>Ongoing Opportunity</span>
                                </div>
                                <div class="group-details-inner">
                                    <div class="detail-column info-block">
                                        <i class="icon-wrap">
                                            <img src="../assets/images/user-icon1.svg" alt="user">
                                        </i>
                                        <div class="text-wrap">
                                            <span class="title-text mb-1">250</span>
                                            <span class="subtitle-text"><em>Already</em> volunteered</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </b-card-body>
                        <b-card-footer>
                            <b-button class="btn-bordersecondary icon-btn" title="Apply Now">
                                <span>Apply</span>
                                <i>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 16" width="19" height="15">
                                        <g id="Main Content">
                                            <g id="1">
                                                <g id="Button">
                                                    <path id="Forma 1 copy 12" class="shp0"
                                                        d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z" />
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </i>
                            </b-button>
                        </b-card-footer>
                    </b-card>
                </b-col>
                 <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
                    <b-card no-body>
                        <b-card-header>
                            <div class="header-img-block">
                                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[1]+')'}">
                                    <img src="../assets/images/group-img1.png" alt="group-img">
                                </b-link>
                                <b-link href="#" class="location" title="location">
                                    <i>
                                        <img src="../assets/images/location.svg" alt="location">
                                    </i>Cape Town
                                </b-link>
                                <b-button class="favourite-icon" v-b-tooltip.hover>
                                    <i class="normal-img">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 21" width="24" height="21">
                                        <g id="Main Content">
                                            <g id="1">
                                                <g id="Image content">
                                                    <path id="Forma 1" d="M22.1 2.86C20.9 1.66 19.3 1 17.59 1C15.89 1 14.29 1.66 13.08 2.86L12.49 3.45L11.89 2.86C10.69 1.66 9.08 1 7.38 1C5.67 1 4.07 1.66 2.87 2.86C0.38 5.34 0.38 9.36 2.87 11.84L11.78 20.71C11.93 20.86 12.11 20.95 12.3 20.98C12.36 20.99 12.43 21 12.49 21C12.74 21 13 20.9 13.19 20.71L22.1 11.84C24.59 9.36 24.59 5.34 22.1 2.86ZM20.71 10.45L12.49 18.64L4.26 10.45C2.54 8.74 2.54 5.96 4.26 4.25C5.09 3.42 6.2 2.96 7.38 2.96C8.56 2.96 9.66 3.42 10.5 4.25L11.79 5.53C12.16 5.9 12.81 5.9 13.18 5.53L14.47 4.25C15.31 3.42 16.41 2.96 17.59 2.96C18.77 2.96 19.88 3.42 20.71 4.25C22.43 5.96 22.43 8.74 20.71 10.45Z" />
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    </i>
                                     <i class="hover-img">
                                       <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 492.7 426.8" style="enable-background:new 0 0 492.7 426.8;" xml:space="preserve">
                                        <g>
                                            <g id="Icons_18_">
                                                <path d="M492.7,133.1C492.7,59.6,433.1,0,359.7,0c-48,0-89.9,25.5-113.3,63.6C222.9,25.5,181,0,133,0
                                                    C59.6,0,0,59.6,0,133.1c0,40,17.7,75.8,45.7,100.2l188.5,188.6c3.2,3.2,7.6,5,12.1,5s8.9-1.8,12.1-5L447,233.2
                                                    C475,208.9,492.7,173.1,492.7,133.1z"/>
                                            </g>
                                        </g>
                                        </svg>
                                    </i>       
                                </b-button>
                                <b-button class="add-icon" title="add">
                                    <img src="../assets/images/add-group-ic.svg" alt="add">
                                </b-button>
                            </div>
							<div class="group-category">
								<span class="category-text">Animals</span>
							</div>
                        </b-card-header>
                        <b-card-body>
                            <div class="content-block">
                                <b-link target="_blank" title="Animal welfare &amp; save birds" class="card-title mb-2">Animal welfare &amp; save birds campaign
                                </b-link>
                                <b-card-text>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                    incididunt
                                    ut labore...
                                </b-card-text>
                                <div class="group-ratings">
                                    <span class="group-name">JR Foundation</span>
                                    <span class="ratings">
                                         <star-rating
										v-bind:increment="0.01"
										v-bind:max-rating="5"
										inactive-color="#dddddd"
										active-color="#F7D341"
										v-bind:star-size="23"
										>
									</star-rating>
                                    </span>
                                </div>
                            </div>
                            <div class="group-details">
                                <div class="top-strip">
                                    <span>Plant 10,000 Trees</span>
                                </div>
                                <div class="group-details-inner has-progress">
                                    <div class="detail-column info-block">
                                        <i class="icon-wrap">
                                            <img src="../assets/images/user-icon.svg">
                                        </i>
                                        <div class="text-wrap">
                                            <span class="title-text mb-1">10</span>
                                            <span class="subtitle-text">{{ $t("label.seats_left")}}</span>
                                        </div>
                                    </div>
                                    <div class="detail-column progress-block">
                                        <i class="icon-wrap">
                                            <img src="../assets/images/target-ic.svg">
                                        </i>
                                        <div class="text-wrap">
                                            <b-progress :value="value" :max="max" class="mb-2"></b-progress>
                                            <span class="subtitle-text">8000 {{ $t("label.achieved")}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </b-card-body>
                        <b-card-footer>
                            <b-button class="btn-bordersecondary icon-btn" :title="$t('label.apply_now')">
                                <span>{{ $t("label.apply")}}</span>
                                <i>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 16" width="19" height="15">
                                        <g id="Main Content">
                                            <g id="1">
                                                <g id="Button">
                                                    <path id="Forma 1 copy 12" class="shp0"
                                                        d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z" />
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </i>
                            </b-button>
                        </b-card-footer>
                    </b-card>
                </b-col>
                 <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
                    <b-card no-body>
                        <b-card-header>
                            <div class="header-img-block">
                                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[2]+')'}">
                                    <img src="../assets/images/group-img1.png" alt="group-img">
                                </b-link>
                                <b-link href="#" class="location" title="location">
                                    <i>
                                        <img src="../assets/images/location.svg" :title="$t('label.location')">
                                    </i>Paris
                                </b-link>
                                <b-button class="favourite-icon" v-b-tooltip.hover
                                    <i class="normal-img">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 21" width="24" height="21">
                                        <g id="Main Content">
                                            <g id="1">
                                                <g id="Image content">
                                                    <path id="Forma 1" d="M22.1 2.86C20.9 1.66 19.3 1 17.59 1C15.89 1 14.29 1.66 13.08 2.86L12.49 3.45L11.89 2.86C10.69 1.66 9.08 1 7.38 1C5.67 1 4.07 1.66 2.87 2.86C0.38 5.34 0.38 9.36 2.87 11.84L11.78 20.71C11.93 20.86 12.11 20.95 12.3 20.98C12.36 20.99 12.43 21 12.49 21C12.74 21 13 20.9 13.19 20.71L22.1 11.84C24.59 9.36 24.59 5.34 22.1 2.86ZM20.71 10.45L12.49 18.64L4.26 10.45C2.54 8.74 2.54 5.96 4.26 4.25C5.09 3.42 6.2 2.96 7.38 2.96C8.56 2.96 9.66 3.42 10.5 4.25L11.79 5.53C12.16 5.9 12.81 5.9 13.18 5.53L14.47 4.25C15.31 3.42 16.41 2.96 17.59 2.96C18.77 2.96 19.88 3.42 20.71 4.25C22.43 5.96 22.43 8.74 20.71 10.45Z" />
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    </i>
                                     <i class="hover-img">
                                       <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 492.7 426.8" style="enable-background:new 0 0 492.7 426.8;" xml:space="preserve">
                                        <g>
                                            <g id="Icons_18_">
                                                <path d="M492.7,133.1C492.7,59.6,433.1,0,359.7,0c-48,0-89.9,25.5-113.3,63.6C222.9,25.5,181,0,133,0
                                                    C59.6,0,0,59.6,0,133.1c0,40,17.7,75.8,45.7,100.2l188.5,188.6c3.2,3.2,7.6,5,12.1,5s8.9-1.8,12.1-5L447,233.2
                                                    C475,208.9,492.7,173.1,492.7,133.1z"/>
                                            </g>
                                        </g>
                                        </svg>
                                    </i>       
                                </b-button>
                                <b-button class="add-icon" title="add">
                                    <img src="../assets/images/add-group-ic.svg" alt="add">
                                </b-button>
                            </div>
							<div class="group-category">
								<span class="category-text">Health</span>
							</div>
                        </b-card-header>
                        <b-card-body>
                            <div class="content-block">
                                <b-link target="_blank" title="Plantation and Afforestation" class="card-title mb-2">Plantation and Afforestation programme 
                                </b-link>
                                <b-card-text>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                    incididunt
                                    ut labore...
                                </b-card-text>
                                <div class="group-ratings">
                                    <span class="group-name">Amaze Doctors</span>
                                    <span class="ratings">
                                        <star-rating
										v-bind:increment="0.01"
										v-bind:max-rating="5"
										inactive-color="#dddddd"
										active-color="#F7D341"
										v-bind:star-size="23"
										>
									</star-rating>
                                    </span>
                                </div>
                            </div>
                            <div class="group-details">
                                <div class="top-strip">
                                    <span>Plant 10,000 Trees</span>
                                </div>
                                <div class="group-details-inner has-progress">
                                    <div class="detail-column progress-block">
                                        <i class="icon-wrap">
                                            <img src="../assets/images/target-ic.svg" alt="user">
                                        </i>
                                        <div class="text-wrap">
                                            <b-progress :value="value" :max="max" class="mb-2"></b-progress>
                                            <span class="subtitle-text">8000 achieved</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </b-card-body>
                        <b-card-footer>
                            <b-button class="btn-bordersecondary icon-btn" title="Apply Now">
                                <span>Apply</span>
                                <i>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 16" width="19" height="15">
                                        <g id="Main Content">
                                            <g id="1">
                                                <g id="Button">
                                                    <path id="Forma 1 copy 12" class="shp0"
                                                        d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z" />
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </i>
                            </b-button>
                        </b-card-footer>
                    </b-card>
                </b-col>
          </b-row>
          <b-modal centered :title="$t('label.search_user')" ref="userDetailModal" 
            :modal-class="myclass" hide-footer>
            <b-alert show :variant="classVariant" dismissible v-model="showErrorDiv"
            >{{ message }}</b-alert>
                <div class="autocomplete-control">
                    <div class="autosuggest-container">
                        <VueAutosuggest 
                        ref="autosuggest"
                        name="user"
                        v-model="query"
                        :suggestions="filteredOptions"
                        @input="onInputChange"
                        @selected="onSelected"
                        :get-suggestion-value="getSuggestionValue"
                        :input-props="{
                        id:'autosuggest__input', 
                        placeholder:autoSuggestPlaceholder,
                        }" >
                        <div slot-scope="{suggestion}">
                        <img :src="suggestion.item.avatar" />
                        <div>
                        {{suggestion.item.first_name}}  {{suggestion.item.last_name}}
                        </div>
                        </div>
                        </VueAutosuggest>
                    </div>
                </div>
            <b-form>
                 <div class="btn-wrap">
                    <b-button @click="$refs.userDetailModal.hide()" class="btn-borderprimary">
                    {{ $t("label.close") }}</b-button>
                    <b-button class="btn-bordersecondary" @click="inviteColleagues" ref="autosuggestSubmit" v-bind:disabled="submitDisable">
                    {{ $t("label.submit") }}</b-button>
                </div>
            </b-form>
        </b-modal>
		  </b-container>
	  </div>
      </main>
       <footer v-if="isShownComponent">
             <TheSecondaryFooter v-if="isShownComponent"></TheSecondaryFooter>
        </footer>
  </div>
</template>

<script>
import AppCustomChip from "../components/AppCustomChip";
import StarRating from 'vue-star-rating';
// import ThePrimaryHeader from "../components/Layouts/ThePrimaryHeader";
// import TheSecondaryHeader from "../components/Layouts/TheSecondaryHeader";
// import TheSecondaryFooter from "../components/Layouts/TheSecondaryFooter";
import { VueAutosuggest } from 'vue-autosuggest';
import carousel from 'vue-owl-carousel';
import {favoriteMission,inviteColleague ,applyMission,searchUser,storeMissionRating,missionVolunteers} from "../services/service";
import SimpleBar from 'simplebar';
import store from "../store";

export default {
  components: {
    AppCustomChip,
    StarRating,
	ThePrimaryHeader : () => import("../components/Layouts/ThePrimaryHeader"),
	TheSecondaryHeader: () => import("../components/Layouts/TheSecondaryHeader"),
	TheSecondaryFooter : () =>  import("../components/Layouts/TheSecondaryFooter"),
	VueAutosuggest,
	SimpleBar,
	carousel
  },
  data() {
    return {
    	isShownComponent :false,
    	missionId :this.$route.params.misisonId,
    	missionAddedToFavoriteByUser : false,
    	query: "",
        selected: "",
        rating:3.5,
        currentPage : 1,
        search : "",
        userList : [],
        rows : 0,
        myclass:["userdetail-modal"],
        currentMissionId : 0,
        invitedUserId : 0,
        showErrorDiv : false,
        message : null,
        classVariant :"success",
        autoSuggestPlaceholder : '',
        submitDisable :true,
        recentVolunterLoader : true,
        bgImage : [
        require("@/assets/images/pdf.svg"),
        require("@/assets/images/doc.svg"),
        require("@/assets/images/xlsx.svg"),
		],
        orgLogo:require("@/assets/images/ces-logo.png"),
        commentImg:[ 
			require("@/assets/images/volunteer3.png"),
			require("@/assets/images/volunteer2.png")
			],
        currentPage: 1,
        grpImages: [
            require("@/assets/images/group-img4.png"),
            require("@/assets/images/group-img5.png"),
            require("@/assets/images/group-img6.png"),
        ],
        volunteerList:[
            // {name: 'Andrew Johnson' , imgSrc:  require("@/assets/images/volunteer1.png") , id:1},
            // {name: 'Charles Vigue' , imgSrc:  require("@/assets/images/volunteer2.png") , id:2},
            // {name: 'Kathryn Roberts' , imgSrc:  require("@/assets/images/volunteer3.png") , id:3},
            // {name: 'Estella Fowles' , imgSrc:  require("@/assets/images/volunteer4.png") , id:4},
            // {name: 'Rose Lewis' , imgSrc:  require("@/assets/images/volunteer5.png") , id:5},
            // {name: 'Raymond Pabon' , imgSrc:  require("@/assets/images/volunteer6.png") , id:6},
            // {name: 'Travis Steen' , imgSrc:  require("@/assets/images/volunteer7.png") , id:7},
            // {name: 'Sarah Santillan' , imgSrc:  require("@/assets/images/volunteer8.png") , id:8},
            // {name: 'Linda Richards' , imgSrc:  require("@/assets/images/volunteer9.png") , id:9},
            // {name: 'Rose Lewis' , imgSrc:  require("@/assets/images/volunteer5.png") , id:13},
            // {name: 'Raymond Pabon' , imgSrc:  require("@/assets/images/volunteer6.png") , id:14},
            // {name: 'Andrew Johnson' , imgSrc:  require("@/assets/images/volunteer1.png") , id:10},
            // {name: 'Charles Vigue' , imgSrc:  require("@/assets/images/volunteer2.png") , id:11},
            // {name: 'Kathryn Roberts' , imgSrc:  require("@/assets/images/volunteer3.png") , id:12},
            // {name: 'Travis Steen' , imgSrc:  require("@/assets/images/volunteer7.png") , id:15},
            // {name: 'Travis Steen' , imgSrc:  require("@/assets/images/volunteer7.png") , id:16},
            // {name: 'Sarah Santillan' , imgSrc:  require("@/assets/images/volunteer8.png") , id:17},
            // {name: 'Linda Richards' , imgSrc:  require("@/assets/images/volunteer9.png") , id:18},
            // {name: 'Rose Lewis' , imgSrc:  require("@/assets/images/volunteer5.png") , id:22},
            // {name: 'Sarah Santillan' , imgSrc:  require("@/assets/images/volunteer8.png") , id:23},
            // {name: 'Linda Richards' , imgSrc:  require("@/assets/images/volunteer9.png") , id:24},
            // {name: 'Andrew Johnson' , imgSrc:  require("@/assets/images/volunteer1.png") , id:19},
            // {name: 'Charles Vigue' , imgSrc:  require("@/assets/images/volunteer2.png") , id:20},
            // {name: 'Kathryn Roberts' , imgSrc:  require("@/assets/images/volunteer3.png") , id:21},
		],
		max: 100,
		value: 70,
    };
  },
  mounted(){
	 var tabItem = document.querySelectorAll(".platform-details-tab .nav-tabs li a")
		tabItem.forEach(function(tabItemEvent){
			tabItemEvent.addEventListener("click", tabsHandle);
		});	
		function tabsHandle(tabsEvent){
			var i, tabContent, tabLinks;
			tabContent = document.getElementsByClassName("tab-content");
			for (i = 0; i < tabContent.length; i++) {
				tabContent[i].style.display = "none";
				if(tabsEvent.currentTarget.getAttribute("data-id") === tabContent[i].getAttribute('id')){
					tabContent[i].style.display = "block";
				}
			}
			tabLinks = document.getElementsByClassName("tablinks");
			for (i = 0; i < tabLinks.length; i++) {
				tabLinks[i].className = tabLinks[i].className.replace(" active", "");
			}
			tabsEvent.currentTarget.className += " active";
		}
   },
   computed: {
   		filteredOptions() {
            if(this.userList){
                return [
                { 
                  data: this.userList.filter(option => {
                    var firstName = option.first_name.toLowerCase();
                    var lastName = option.last_name.toLowerCase();
                    var email = option.email.toLowerCase();
                    var searchString = firstName+''+lastName+''+email;
                    return searchString.indexOf(this.query.toLowerCase()) > -1;
                  })
                }
                ];
            }
        },
		perPage(){
			if(screen.width > 767){
				return 9;
			}
			else{
				return 8;
			}
		},

   },
   methods: {
   		setRating: function(rating){
			let missionData = {
				mission_id : '',
				rating : ''
			};
			missionData.mission_id = this.missionId;
			missionData.rating = this.rating;
		    storeMissionRating(missionData).then(response => {
		        if(response.error == true){
		            this.makeToast("danger",response.message);
		        } else {
		            this.makeToast("success",response.message);
		        } 
		    });
	    },
		// Add mission to favorite
		favoriteMission(missionId){
		    let missionData = {
		        mission_id : ''
		    };
		    missionData.mission_id = missionId;
		    favoriteMission(missionData).then(response => {
		        if(response.error == true){
		            this.makeToast("danger",response.message);
		        } else {
		            this.makeToast("success",response.message);
		            this.missionAddedToFavoriteByUser = !this.missionAddedToFavoriteByUser;
		        } 
		    });
		    
		},
		onInputChange(text) {   
             this.submitDisable =true;
        },
        // For selected user id.
        onSelected(item) {
            this.selected = item.item;
            this.submitDisable = false;  
            this.invitedUserId = item.item.user_id;
        },
        //This is what the <input/> value is set to when you are selecting a suggestion.
        getSuggestionValue(suggestion) {
           var firstName = suggestion.item.first_name;
           var lastName = suggestion.item.last_name;
           return firstName+' '+lastName;
        },
		// Open auto suggest modal
        handleModal(missionId){
            this.autoSuggestPlaceholder = this.$i18n.t("label.search_user")
            this.showErrorDiv = false;
            this.message = null;
            this.$refs.userDetailModal.show();
            this.currentMission = missionId;
            setTimeout(() => {
                var onFocus = document.getElementById('autosuggest');
                    onFocus.addEventListener("click", function(){
                        var myElement = document.querySelector('.autosuggest__results');
                        new SimpleBar(myElement, { autoHide: true });   
                    });
            });
        },
		// invite collegues api call
        inviteColleagues(){
            let inviteData = {};
            inviteData.mission_id = this.currentMission;
            inviteData.to_user_id=this.invitedUserId;
            inviteColleague(inviteData).then(response => {
                this.submitDisable =true;
                if (response.error == true) {
                    this.classVariant = "danger";
                    this.message = response.message;
                    this.$refs.autosuggest.$data.currentIndex = null;
                    this.$refs.autosuggest.$data.internalValue = '';
                    this.showErrorDiv = true;
                } else {
                    this.query ="";
                    this.selected = "";
                    this.currentMissionId = 0;
                    this.invitedUserId = 0;
                    this.$refs.autosuggest.$data.currentIndex = null;
                    this.$refs.autosuggest.$data.internalValue = '';
                    this.classVariant = "success";
                    this.message = response.message;
                    this.showErrorDiv = true;
                }
            })
        },
        // missionVolunteers
        getMissionVolunteers(){
        	let missionData = {
				mission_id : '',
				page : ''
			};
			missionData.mission_id = this.$route.params.misisonId;
			missionData.page = this.currentPage;
	    	if (missionData.mission_id) {
	    		this.recentVolunterLoader = true;
	        	missionVolunteers(missionData).then(response =>{
	        		
			        if (!response.error) {
			        	this.volunteerList = response.data;
			        	if (response.pagination) {
			        		this.rows = response.pagination.total
			        	}
	        			this.recentVolunterLoader = false;
	        		}
	        	})			
	        }
    	},

    	searchUsers() {
			searchUser().then(userResponse => {
				this.userList = userResponse;
				this.getMissionVolunteers();
				this.isShownComponent = true;
	        });
    	},

    	pageChange (page) {
            //Change pagination
            this.currentPage = page;
            this.getMissionVolunteers();
        },
		makeToast(variant = null,message) {
            this.$bvToast.toast(message, {
                variant: variant,
                solid: true,
                autoHideDelay: 3000
            })
        },
      handleSliderClick(event){
			event.stopPropagation()
			var hideVideo = document.querySelector(".video-wrap");
			var galleryImg = document.querySelector(".gallery-top .img-wrap");
			var galleryImgSrc = document.querySelector(".gallery-top .img-wrap img");
			var videoSrc =  document.querySelector(".video-wrap iframe");
			var dataSrc = event.target.getAttribute('data-src');
			if(event.target.classList.contains("video-item")){
				videoSrc.src = dataSrc
				hideVideo.style.display = "block";
				galleryImg.style.display = "none";	
			}
			else if(event.target.classList.contains("btn-play")){
				var parentBtn = event.target.parentNode;
				var siblingBtn = parentBtn.childNodes;
				hideVideo.style.display = "block";
				galleryImg.style.display = "none";
				videoSrc.src = siblingBtn[0].getAttribute('data-src')
			}
			else{
				 galleryImgSrc.src = event.target.src ;		
				galleryImg.style.display = "block";
				hideVideo.style.display = "none";
			}
		}
   },
	created(){
		var _this = this;
		if(store.state.search != null) {
	        this.search = store.state.search;
	    } else {
	    	this.search = '';
	    }
	    this.searchUsers();

	    var globalThis = this;
		 setTimeout(() => {
		  var thumbImg = document.querySelectorAll(".gallery-thumbs .owl-item img, .gallery-thumbs .owl-item .btn-play");
			thumbImg.forEach(function(itemEvent){
				itemEvent.removeEventListener("click", globalThis.handleSliderClick);	
				itemEvent.addEventListener("click", globalThis.handleSliderClick);	
			});
			
		});
		window.addEventListener('resize', function() {
			setTimeout(() => {
				var thumbImg = document.querySelectorAll(".gallery-thumbs .owl-item img, .gallery-thumbs .owl-item .btn-play");
					thumbImg.forEach(function(itemEvent){
					itemEvent.removeEventListener("click", globalThis.handleSliderClick);	
					itemEvent.addEventListener("click", globalThis.handleSliderClick);	
				});
			},2000);
		});
	    // Get mission volunteers
	    // this.getMissionVolunteers();
	},
   updated(){
	
   }
};
</script>


