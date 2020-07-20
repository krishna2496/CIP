<template>
	<div class="cards-wrapper">
		<div class="card-grid">
			<b-row>
				<b-col lg="4" sm="6" class="card-outer" id="grid-card01">
					<div class="card-inner">
					 <b-card no-body>
					 	<div class="location">
							<i>
								<img :src="$store.state.imagePath+'/assets/images/location.svg'" :alt="languageData.label.location">
							</i>
							Paris
						</div>
            			<b-card-header>
            				 <div class="header-img-block">
            				 	<div class="group-img" :style="{backgroundImage: 'url('+grpImages[0]+')'}">
								</div>
            				 </div>
            				 <div class="group-category"><span class="category-text">Environment</span></div>
            			</b-card-header>
            			<b-card-body>
            				<div class="content-block">
								<div class="mission-label-wrap">
									<div class="mission-label volunteer-label">
										<span><i class="icon-wrap"><img src="../assets/images/volunteer-icon.svg" alt="volunteer icon"></i>Volunteer</span>
									</div>
									<div class="mission-label virtual-label">
										<span>Virtual</span>
									</div>
								</div>
								<div class="content-inner-block">
									<b-link target="_blank" class="card-title mb-2">
										Abandoned Pets Caretaker
									</b-link>
									<div class="group-ratings">
										<star-rating  v-bind:increment="1" v-bind:max-rating="5"
													 inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18"
													  :read-only="true" rating="4.5">
										</star-rating>
									</div>
									<b-card-text>
										Take this chance to take care of abused and abandoned pets at a center located in the Greater Vancouver area of Canada.
									</b-card-text>
								</div>
								<div class="event-block">
									<p class="event-name">For <span>Friendly Paws</span></p>
									<b-button class="like-btn">
										<img src="../assets/images/heart-fill-icon.svg" alt="Heart Icon"/>
									</b-button>
								</div>
							</div>
							<div class="init-hidden">
								<div class="group-details">
									<div class="top-strip">
										<span>From 04/07/2020 Until 05/07/2020</span>
									</div>
									<div class="group-details-inner volunteer-progress">
										<div class="detail-column info-block">
											<i class="icon-wrap">
												<img :src="$store.state.imagePath+'/assets/images/user-icon.svg'" alt="user">
											</i>
											<div class="text-wrap">
												<span class="title-text">149</span>
												<span class="subtitle-text">Seats left</span>
											</div>
										</div>
										<div class="detail-column progress-block ">
											<i class="icon-wrap">
												<img :src="$store.state.imagePath+'/assets/images/target-ic.svg'"
													 alt="user">
											</i>
											<div class="text-wrap">
												<b-progress :value="value" :max="max"
													class="mb-2"></b-progress>
												<span class="subtitle-text">5000
                                                <span>achieved
                                                </span>
                                            </span>
											</div>
										</div>
									</div>
								</div>
								<div class="card-action-block">
									<div class="left-btn">
										<b-link class="btn-bordersecondary icon-btn">
											<span>View details</span>
											<i class="icon-wrap">
												<svg width="18" height="9" viewBox="0 0 18 9" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M17.3571 4.54129C17.3571 4.63504 17.3237 4.7154 17.2567 4.78237L13.3996 8.33817C13.2924 8.43192 13.1752 8.45201 13.048 8.39844C12.9208 8.33817 12.8571 8.24107 12.8571 8.10714V5.85714H0.321429C0.227679 5.85714 0.15067 5.82701 0.0904018 5.76674C0.0301339 5.70647 0 5.62946 0 5.53571V3.60714C0 3.51339 0.0301339 3.43638 0.0904018 3.37612C0.15067 3.31585 0.227679 3.28571 0.321429 3.28571H12.8571V1.03571C12.8571 0.895089 12.9208 0.797991 13.048 0.744419C13.1752 0.690848 13.2924 0.707589 13.3996 0.794642L17.2567 4.31027C17.3237 4.37723 17.3571 4.45424 17.3571 4.54129Z" fill="#ffffff"/>
												</svg>
											</i>
										</b-link>
									</div>
									<div class="social-btn">
										<b-button class="icon-btn"><img src="../assets/images/multi-user-icon.svg" alt="multi user icon"></b-button>
										<!-- <b-button class="icon-btn"><img src="../assets/images/heart-icon.svg" alt="heart icon"></b-button> -->
										<b-button class="icon-btn fill-heart-btn"><img src="../assets/images/heart-fill-icon.svg" alt="heart icon"></b-button>
									</div>
								</div>
							</div>
            			</b-card-body>
            		 </b-card>
            		</div>
				</b-col>
				<b-col lg="4" sm="6" class="card-outer" id="grid-card02">
					<div class="card-inner">
					 <b-card no-body>
					 	<div class="location">
							<i>
								<img :src="$store.state.imagePath+'/assets/images/location.svg'" :alt="languageData.label.location">
							</i>
							Paris
						</div>
            			<b-card-header>
            				 <div class="header-img-block">
            				 	<div class="group-img" :style="{backgroundImage: 'url('+grpImages[1]+')'}">
								</div>
            				 </div>
            				 <div class="group-category"><span class="category-text">Reforestation</span></div>
            			</b-card-header>
            			<b-card-body>
            				<div class="content-block">
								<div class="mission-label-wrap">
									<div class="mission-label volunteer-label">
										<span><i class="icon-wrap"><img src="../assets/images/volunteer-icon.svg" alt="volunteer icon"></i>Volunteer</span>
									</div>
								</div>
								<div class="content-inner-block">
									<b-link target="_blank" class="card-title mb-2">
										Donate $1 to plant a tree
									</b-link>
									<div class="group-ratings">
										<star-rating  v-bind:increment="0.5" v-bind:max-rating="5"
													 inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18"
													  :read-only="true" rating="4.5">
										</star-rating>
									</div>
									<b-card-text>
										Take this chance to take care of abused and abandoned pets at a center located in the Greater Vancouver area of Canada.
									</b-card-text>
								</div>
								<div class="event-block">
									<p class="event-name">For <span>Friendly Paws</span></p>
								</div>
							</div>
							<div class="init-hidden">
								<div class="group-details">
									<div class="top-strip">
										<span>From 04/07/2020 Until 05/07/2020</span>
									</div>
									<div class="group-details-inner">
										<div class="detail-column info-block">
											<i class="icon-wrap">
												<img :src="$store.state.imagePath+'/assets/images/user-icon.svg'" alt="user">
											</i>
											<div class="text-wrap">
												<span class="title-text">149</span>
												<span class="subtitle-text">Seats left</span>
											</div>
										</div>
										<div class="detail-column info-block">
											<i class="icon-wrap">
												<img :src="$store.state.imagePath+'/assets/images/clock.svg'" alt="user">
											</i>
											<div class="text-wrap">
												<span class="title-text">09/05/2020</span>
												<span class="subtitle-text">Registration deadline</span>
											</div>
										</div>
									</div>
								</div>
								<div class="card-action-block">
									<div class="left-btn">
										<b-link class="btn-bordersecondary icon-btn">
											<span>View details</span>
											<i class="icon-wrap">
												<svg width="18" height="9" viewBox="0 0 18 9" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M17.3571 4.54129C17.3571 4.63504 17.3237 4.7154 17.2567 4.78237L13.3996 8.33817C13.2924 8.43192 13.1752 8.45201 13.048 8.39844C12.9208 8.33817 12.8571 8.24107 12.8571 8.10714V5.85714H0.321429C0.227679 5.85714 0.15067 5.82701 0.0904018 5.76674C0.0301339 5.70647 0 5.62946 0 5.53571V3.60714C0 3.51339 0.0301339 3.43638 0.0904018 3.37612C0.15067 3.31585 0.227679 3.28571 0.321429 3.28571H12.8571V1.03571C12.8571 0.895089 12.9208 0.797991 13.048 0.744419C13.1752 0.690848 13.2924 0.707589 13.3996 0.794642L17.2567 4.31027C17.3237 4.37723 17.3571 4.45424 17.3571 4.54129Z" fill="#ffffff"/>
												</svg>
											</i>
										</b-link>
									</div>
									<div class="social-btn">
										<b-button class="icon-btn"><img src="../assets/images/multi-user-icon.svg" alt="multi user icon"></b-button>
										<b-button class="icon-btn"><img src="../assets/images/heart-icon.svg" alt="heart icon"></b-button>
										<!-- <b-button class="icon-btn fill-heart-btn"><img src="../assets/images/heart-fill-icon.svg" alt="heart icon"></b-button> -->
									</div>
								</div>
							</div>
            			</b-card-body>
            		 </b-card>
            		</div>
				</b-col>
				<b-col lg="4" sm="6" class="card-outer" id="grid-card03">
					<div class="card-inner">
					 <b-card no-body>
					 	<div class="location">
							<i>
								<img :src="$store.state.imagePath+'/assets/images/location.svg'" :alt="languageData.label.location">
							</i>
							Paris
						</div>
            			<b-card-header>
            				 <div class="header-img-block">
            				 	<div class="group-img" :style="{backgroundImage: 'url('+grpImages[0]+')'}">
								</div>
            				 </div>
            				 <div class="group-category"><span class="category-text">Environment</span></div>
            			</b-card-header>
            			<b-card-body>
            				<div class="content-block">
								<div class="mission-label-wrap">
									<div class="mission-label volunteer-label">
										<span><i class="icon-wrap"><img src="../assets/images/volunteer-icon.svg" alt="volunteer icon"></i>Volunteer</span>
									</div>
									<div class="mission-label virtual-label">
										<span>Virtual</span>
									</div>
								</div>
								<div class="content-inner-block">
									<b-link target="_blank" class="card-title mb-2">
										Abandoned Pets Caretaker
									</b-link>
									<div class="group-ratings">
										<star-rating  v-bind:increment="0.5" v-bind:max-rating="5"
													 inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18"
													  :read-only="true" rating="4.5">
										</star-rating>
									</div>
									<b-card-text>
										Take this chance to take care of abused and abandoned pets at a center located in the Greater Vancouver area of Canada.
									</b-card-text>
								</div>
								<div class="event-block">
									<p class="event-name">For <span>Friendly Paws</span></p>
								</div>
							</div>
							<div class="init-hidden">
								<div class="group-details">
									<div class="top-strip">
										<span>From 04/07/2020 Until 05/07/2020</span>
									</div>
									<div class="group-details-inner">
										<div class="detail-column info-block">
											<i class="icon-wrap">
												<img :src="$store.state.imagePath+'/assets/images/user-icon.svg'" alt="user">
											</i>
											<div class="text-wrap">
												<span class="title-text">149</span>
												<span class="subtitle-text">Seats left</span>
											</div>
										</div>
										<div class="detail-column info-block">
											<i class="icon-wrap">
												<img :src="$store.state.imagePath+'/assets/images/clock.svg'" alt="user">
											</i>
											<div class="text-wrap">
												<span class="title-text">09/05/2020</span>
												<span class="subtitle-text">Registration deadline</span>
											</div>
										</div>
									</div>
								</div>
								<div class="card-action-block">
									<div class="left-btn">
										<b-link class="btn-bordersecondary icon-btn">
											<span>View details</span>
											<i class="icon-wrap">
												<svg width="18" height="9" viewBox="0 0 18 9" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M17.3571 4.54129C17.3571 4.63504 17.3237 4.7154 17.2567 4.78237L13.3996 8.33817C13.2924 8.43192 13.1752 8.45201 13.048 8.39844C12.9208 8.33817 12.8571 8.24107 12.8571 8.10714V5.85714H0.321429C0.227679 5.85714 0.15067 5.82701 0.0904018 5.76674C0.0301339 5.70647 0 5.62946 0 5.53571V3.60714C0 3.51339 0.0301339 3.43638 0.0904018 3.37612C0.15067 3.31585 0.227679 3.28571 0.321429 3.28571H12.8571V1.03571C12.8571 0.895089 12.9208 0.797991 13.048 0.744419C13.1752 0.690848 13.2924 0.707589 13.3996 0.794642L17.2567 4.31027C17.3237 4.37723 17.3571 4.45424 17.3571 4.54129Z" fill="#ffffff"/>
												</svg>
											</i>
										</b-link>
									</div>
									<div class="social-btn">
										<b-button class="icon-btn"><img src="../assets/images/multi-user-icon.svg" alt="multi user icon"></b-button>
										<b-button class="icon-btn"><img src="../assets/images/heart-icon.svg" alt="heart icon"></b-button>
										<!-- <b-button class="icon-btn fill-heart-btn"><img src="../assets/images/heart-fill-icon.svg" alt="heart icon"></b-button> -->
									</div>
								</div>
							</div>
            			</b-card-body>
            		 </b-card>
            		</div>
				</b-col>
				<b-col lg="4" sm="6" class="card-outer" id="grid-card04">
					<div class="card-inner">
					 <b-card no-body class="donate-card">
					 	<div class="location">
							<i>
								<img :src="$store.state.imagePath+'/assets/images/location.svg'" :alt="languageData.label.location">
							</i>
							Paris
						</div>
            			<b-card-header>
            				 <div class="header-img-block">
            				 	<div class="group-img" :style="{backgroundImage: 'url('+grpImages[2]+')'}">
								</div>
            				 </div>
            				 <div class="group-category"><span class="category-text">Education</span></div>
            			</b-card-header>
            			<b-card-body>
            				<div class="content-block">
								<div class="mission-label-wrap">
									<div class="mission-label donation-label">
										<span><i class="icon-wrap"><img src="../assets/images/donation-icon.svg" alt=""></i>Donation</span>
									</div>
								</div>
								<div class="content-inner-block">
									<b-link target="_blank" class="card-title mb-2">
										Child immigration health
									</b-link>
									<div class="group-ratings">
										<star-rating  v-bind:increment="0.5" v-bind:max-rating="5"
													 inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18"
													  :read-only="true" rating="4.5">
										</star-rating>
									</div>
									<b-card-text>
										Take this chance to take care of abused and abandoned pets at a center located in the Greater Vancouver area of Canada.
									</b-card-text>
								</div>
								<div class="event-block has-progress">
									<p class="event-name">For <span>Friendly Paws</span></p>
									<div class="progress-block detail-column" >
										<div class="text-wrap">
											<b-progress :value="value" :max="max" ></b-progress>	
                                            <div class="progress-info">
												<span class="subtitle-text">
		                                            <em> 70% </em>
		                                            <em>Achieved</em>
                                            	</span>
	                                            <span class="subtitle-text">
	                                            	<em><b>$250,000</b></em>
	                                            	<em>Goal</em>
	                                            </span>
                                            </div>
										</div>
									</div>
									<b-button class="like-btn">
										<img src="../assets/images/heart-fill-icon.svg" alt="Heart Icon"/>
									</b-button>
								</div>
							</div>
							<div class="init-hidden">
								<div class="group-details">
									<div class="top-strip">
										<span>From 04/07/2020 Until 05/07/2020</span>
									</div>
									<div class="group-details-inner has-progress">
										<div class="detail-column progress-block">
											<div class="text-wrap">
												<p><b>$210,851</b> raised by <b>25 Donors</b></p>
												<b-progress :value="value" :max="max"></b-progress>
												<div class="progress-info">
													<span class="subtitle-text">
		                                                70% 
		                                                <em>Achieved</em>
		                                            </span>
			                                        <span class="subtitle-text">
		                                            	<em>$250,000</em>
		                                            	<em>Goal</em>
			                                        </span>
		                                        </div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-action-block">
									<div class="left-btn">
										<b-link class="btn-bordersecondary icon-btn">
											<span>View details</span>
											<i class="icon-wrap">
												<svg width="18" height="9" viewBox="0 0 18 9" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M17.3571 4.54129C17.3571 4.63504 17.3237 4.7154 17.2567 4.78237L13.3996 8.33817C13.2924 8.43192 13.1752 8.45201 13.048 8.39844C12.9208 8.33817 12.8571 8.24107 12.8571 8.10714V5.85714H0.321429C0.227679 5.85714 0.15067 5.82701 0.0904018 5.76674C0.0301339 5.70647 0 5.62946 0 5.53571V3.60714C0 3.51339 0.0301339 3.43638 0.0904018 3.37612C0.15067 3.31585 0.227679 3.28571 0.321429 3.28571H12.8571V1.03571C12.8571 0.895089 12.9208 0.797991 13.048 0.744419C13.1752 0.690848 13.2924 0.707589 13.3996 0.794642L17.2567 4.31027C17.3237 4.37723 17.3571 4.45424 17.3571 4.54129Z" fill="#ffffff"/>
												</svg>
											</i>
										</b-link>
									</div>
									<div class="social-btn">
										<b-button class="icon-btn"><img src="../assets/images/multi-user-icon.svg" alt="multi user icon"></b-button>
										<!-- <b-button class="icon-btn"><img src="../assets/images/heart-icon.svg" alt="heart icon"></b-button> -->
										<b-button class="icon-btn fill-heart-btn"><img src="../assets/images/heart-fill-icon.svg" alt="heart icon"></b-button>
									</div>
								</div>
							</div>
            			</b-card-body>
            		 </b-card>
            		</div>
				</b-col>
				<b-col lg="4" sm="6" class="card-outer" id="grid-card05">
					<div class="card-inner">
					 <b-card no-body class="donate-card">
					 	<div class="location">
							<i>
								<img :src="$store.state.imagePath+'/assets/images/location.svg'" :alt="languageData.label.location">
							</i>
							Paris
						</div>
            			<b-card-header>
            				 <div class="header-img-block">
            				 	<div class="group-img" :style="{backgroundImage: 'url('+grpImages[2]+')'}">
								</div>
            				 </div>
            				 <div class="group-category"><span class="category-text">Education</span></div>
            			</b-card-header>
            			<b-card-body>
            				<div class="content-block">
								<div class="mission-label-wrap">
									<div class="mission-label donation-label">
										<span><i class="icon-wrap"><img src="../assets/images/donation-icon.svg" alt=""></i>Donation</span>
									</div>
								</div>
								<div class="content-inner-block">
									<b-link target="_blank" class="card-title mb-2">
										Child immigration health
									</b-link>
									<div class="group-ratings">
										<star-rating  v-bind:increment="0.5" v-bind:max-rating="5"
													 inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18"
													  :read-only="true" rating="4.5">
										</star-rating>
									</div>
									<b-card-text>
										Take this chance to take care of abused and abandoned pets at a center located in the Greater Vancouver area of Canada.
									</b-card-text>
								</div>
								<div class="event-block has-progress">
									<p class="event-name">For <span>Friendly Paws</span></p>
									<div class="progress-block detail-column" >
										<div class="text-wrap">
											<b-progress :value="value" :max="max" ></b-progress>	
                                            <div class="progress-info">
												<span class="subtitle-text">
		                                            <em> 70% </em>
		                                            <em>Achieved</em>
                                            	</span>
	                                            <span class="subtitle-text">
	                                            	<em><b>$250,000</b></em>
	                                            	<em>Goal</em>
	                                            </span>
                                            </div>
										</div>
									</div>
									<b-button class="like-btn">
										<img src="../assets/images/heart-fill-icon.svg" alt="Heart Icon"/>
									</b-button>
								</div>
							</div>
							<div class="init-hidden">
								<div class="group-details">
									<div class="top-strip">
										<span>From 04/07/2020 Until 05/07/2020</span>
									</div>
									<div class="group-details-inner has-progress">
										<div class="detail-column progress-block">
											<div class="text-wrap">
												<p><b>$210,851</b> raised by <b>25 Donors</b></p>
												<b-progress :value="value" :max="max"></b-progress>
												<div class="progress-info">
													<span class="subtitle-text">
		                                                70% 
		                                                <em>Achieved</em>
		                                            </span>
			                                        <span class="subtitle-text">
		                                            	<em>$250,000</em>
		                                            	<em>Goal</em>
			                                        </span>
		                                        </div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-action-block">
									<div class="left-btn">
										<b-link class="btn-bordersecondary icon-btn">
											<span>View details</span>
											<i class="icon-wrap">
												<svg width="18" height="9" viewBox="0 0 18 9" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M17.3571 4.54129C17.3571 4.63504 17.3237 4.7154 17.2567 4.78237L13.3996 8.33817C13.2924 8.43192 13.1752 8.45201 13.048 8.39844C12.9208 8.33817 12.8571 8.24107 12.8571 8.10714V5.85714H0.321429C0.227679 5.85714 0.15067 5.82701 0.0904018 5.76674C0.0301339 5.70647 0 5.62946 0 5.53571V3.60714C0 3.51339 0.0301339 3.43638 0.0904018 3.37612C0.15067 3.31585 0.227679 3.28571 0.321429 3.28571H12.8571V1.03571C12.8571 0.895089 12.9208 0.797991 13.048 0.744419C13.1752 0.690848 13.2924 0.707589 13.3996 0.794642L17.2567 4.31027C17.3237 4.37723 17.3571 4.45424 17.3571 4.54129Z" fill="#ffffff"/>
												</svg>
											</i>
										</b-link>
									</div>
									<div class="social-btn">
										<b-button class="icon-btn"><img src="../assets/images/multi-user-icon.svg" alt="multi user icon"></b-button>
										<!-- <b-button class="icon-btn"><img src="../assets/images/heart-icon.svg" alt="heart icon"></b-button> -->
										<b-button class="icon-btn fill-heart-btn"><img src="../assets/images/heart-fill-icon.svg" alt="heart icon"></b-button>
									</div>
								</div>
							</div>
            			</b-card-body>
            		 </b-card>
            		</div>
				</b-col>
				<b-col lg="4" sm="6" class="card-outer" id="grid-card06">
					<div class="card-inner">
					 <b-card no-body>
					 	<div class="location">
							<i>
								<img :src="$store.state.imagePath+'/assets/images/location.svg'" :alt="languageData.label.location">
							</i>
							Paris
						</div>
            			<b-card-header>
            				 <div class="header-img-block">
            				 	<div class="group-img" :style="{backgroundImage: 'url('+grpImages[3]+')'}">
								</div>
            				 </div>
            				 <div class="group-category"><span class="category-text">Environment</span></div>
            			</b-card-header>
            			<b-card-body>
            				<div class="content-block">
								<div class="mission-label-wrap">
									<div class="mission-label volunteer-label">
										<span><i class="icon-wrap"><img src="../assets/images/volunteer-icon.svg" alt="volunteer icon"></i>Volunteer</span>
									</div>
									<div class="mission-label virtual-label">
										<span>Virtual</span>
									</div>
								</div>
								<div class="content-inner-block">
									<b-link target="_blank" class="card-title mb-2">
										#LGBTQIAPK pride
									</b-link>
									<div class="group-ratings">
										<star-rating  v-bind:increment="0.5" v-bind:max-rating="5"
													 inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18"
													  :read-only="true" rating="4.5">
										</star-rating>
									</div>
									<b-card-text>
										Take this chance to take care of abused and abandoned pets at a center located in the Greater Vancouver area of Canada.
									</b-card-text>
								</div>
								<div class="event-block">
									<p class="event-name">For <span>Friendly Paws</span></p>
								</div>
							</div>
							<div class="init-hidden">
								<div class="group-details">
									<div class="top-strip">
										<span>From 04/07/2020 Until 05/07/2020</span>
									</div>
									<div class="group-details-inner">
										<div class="detail-column info-block">
											<i class="icon-wrap">
												<img :src="$store.state.imagePath+'/assets/images/user-icon.svg'" alt="user">
											</i>
											<div class="text-wrap">
												<span class="title-text">149</span>
												<span class="subtitle-text">Seats left</span>
											</div>
										</div>
										<div class="detail-column info-block">
											<i class="icon-wrap">
												<img :src="$store.state.imagePath+'/assets/images/clock.svg'" alt="user">
											</i>
											<div class="text-wrap">
												<span class="title-text">09/05/2020</span>
												<span class="subtitle-text">Registration deadline</span>
											</div>
										</div>
									</div>
								</div>
								<div class="card-action-block">
									<div class="left-btn">
										<b-link class="btn-bordersecondary icon-btn">
											<span>View details</span>
											<i class="icon-wrap">
												<svg width="18" height="9" viewBox="0 0 18 9" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M17.3571 4.54129C17.3571 4.63504 17.3237 4.7154 17.2567 4.78237L13.3996 8.33817C13.2924 8.43192 13.1752 8.45201 13.048 8.39844C12.9208 8.33817 12.8571 8.24107 12.8571 8.10714V5.85714H0.321429C0.227679 5.85714 0.15067 5.82701 0.0904018 5.76674C0.0301339 5.70647 0 5.62946 0 5.53571V3.60714C0 3.51339 0.0301339 3.43638 0.0904018 3.37612C0.15067 3.31585 0.227679 3.28571 0.321429 3.28571H12.8571V1.03571C12.8571 0.895089 12.9208 0.797991 13.048 0.744419C13.1752 0.690848 13.2924 0.707589 13.3996 0.794642L17.2567 4.31027C17.3237 4.37723 17.3571 4.45424 17.3571 4.54129Z" fill="#ffffff"/>
												</svg>
											</i>
										</b-link>
									</div>
									<div class="social-btn">
										<b-button class="icon-btn"><img src="../assets/images/multi-user-icon.svg" alt="multi user icon"></b-button>
										<b-button class="icon-btn"><img src="../assets/images/heart-icon.svg" alt="heart icon"></b-button>
										<!-- <b-button class="icon-btn fill-heart-btn"><img src="../assets/images/heart-fill-icon.svg" alt="heart icon"></b-button> -->
									</div>
								</div>
							</div>
            			</b-card-body>
            		 </b-card>
            		</div>
				</b-col>
				<b-col lg="4" sm="6" class="card-outer" id="grid-card07">
					<div class="card-inner">
					 <b-card no-body class="donate-card">
					 	<div class="location">
							<i>
								<img :src="$store.state.imagePath+'/assets/images/location.svg'" :alt="languageData.label.location">
							</i>
							Paris
						</div>
            			<b-card-header>
            				 <div class="header-img-block">
            				 	<div class="group-img" :style="{backgroundImage: 'url('+grpImages[4]+')'}">
								</div>
            				 </div>
            				 <div class="group-category"><span class="category-text">Culture</span></div>
            			</b-card-header>
            			<b-card-body>
            				<div class="content-block">
								<div class="mission-label-wrap">
									<div class="mission-label donation-label">
										<span><i class="icon-wrap"><img src="../assets/images/donation-icon.svg" alt=""></i>Donation</span>
									</div>
								</div>
								<div class="content-inner-block">
									<b-link target="_blank" class="card-title mb-2">
										Donate $50 for art school
									</b-link>
									<div class="group-ratings">
										<star-rating  v-bind:increment="0.5" v-bind:max-rating="5"
													 inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18"
													  :read-only="true" rating="4.5">
										</star-rating>
									</div>
									<b-card-text>
										Take this chance to take care of abused and abandoned pets at a center located in the Greater Vancouver area of Canada.
									</b-card-text>
								</div>
								<div class="event-block has-progress">
									<p class="event-name">For <span>Artists Club SRL</span></p>
									<div class="progress-block detail-column success-donate" >
										<div class="text-wrap">	
												<p><b class="donate-success">$210,851</b> raised by 25 donors</p>
										</div>
									</div>
								</div>
							</div>
							<div class="init-hidden">
								<div class="group-details">
									<div class="top-strip">
										<span>From 04/07/2020 Until 05/07/2020</span>
									</div>
									<div class="group-details-inner">
										<div class="detail-column progress-block donate-success-detail">
											<div class="text-wrap">
												<p><b>$210,851</b> raised by <b>25 Donors</b></p>
											</div>
										</div>
									</div>
								</div>
								<div class="card-action-block">
									<div class="left-btn">
										<b-link class="btn-bordersecondary icon-btn">
											<span>View details</span>
											<i class="icon-wrap">
												<svg width="18" height="9" viewBox="0 0 18 9" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M17.3571 4.54129C17.3571 4.63504 17.3237 4.7154 17.2567 4.78237L13.3996 8.33817C13.2924 8.43192 13.1752 8.45201 13.048 8.39844C12.9208 8.33817 12.8571 8.24107 12.8571 8.10714V5.85714H0.321429C0.227679 5.85714 0.15067 5.82701 0.0904018 5.76674C0.0301339 5.70647 0 5.62946 0 5.53571V3.60714C0 3.51339 0.0301339 3.43638 0.0904018 3.37612C0.15067 3.31585 0.227679 3.28571 0.321429 3.28571H12.8571V1.03571C12.8571 0.895089 12.9208 0.797991 13.048 0.744419C13.1752 0.690848 13.2924 0.707589 13.3996 0.794642L17.2567 4.31027C17.3237 4.37723 17.3571 4.45424 17.3571 4.54129Z" fill="#ffffff"/>
												</svg>
											</i>
										</b-link>
									</div>
									<div class="social-btn">
										<b-button class="icon-btn"><img src="../assets/images/multi-user-icon.svg" alt="multi user icon"></b-button>
										<b-button class="icon-btn"><img src="../assets/images/heart-icon.svg" alt="heart icon"></b-button>
										<!-- <b-button class="icon-btn fill-heart-btn"><img src="../assets/images/heart-fill-icon.svg" alt="heart icon"></b-button> -->
									</div>
								</div>
							</div>
            			</b-card-body>
            		 </b-card>
            		</div>
				</b-col>
				<b-col lg="4" sm="6" class="card-outer" id="grid-card08">
					<div class="card-inner">
					 <b-card no-body class="donate-card">
					 	<div class="location">
							<i>
								<img :src="$store.state.imagePath+'/assets/images/location.svg'" :alt="languageData.label.location">
							</i>
							Paris
						</div>
            			<b-card-header>
            				 <div class="header-img-block">
            				 	<div class="group-img" :style="{backgroundImage: 'url('+grpImages[4]+')'}">
								</div>
            				 </div>
            				 <div class="group-category"><span class="category-text">Culture</span></div>
            			</b-card-header>
            			<b-card-body>
            				<div class="content-block">
								<div class="mission-label-wrap">
									<div class="mission-label donation-label">
										<span><i class="icon-wrap"><img src="../assets/images/donation-icon.svg" alt=""></i>Donation</span>
									</div>
								</div>
								<div class="content-inner-block">
									<b-link target="_blank" class="card-title mb-2">
										Donate $50 for art school
									</b-link>
									<div class="group-ratings">
										<star-rating  v-bind:increment="0.5" v-bind:max-rating="5"
													 inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18"
													  :read-only="true" rating="4.5">
										</star-rating>
									</div>
									<b-card-text>
										Take this chance to take care of abused and abandoned pets at a center located in the Greater Vancouver area of Canada.
									</b-card-text>
								</div>
								<div class="event-block has-progress">
									<p class="event-name">For <span>Artists Club SRL</span></p>
									<div class="progress-block detail-column success-donate" >
										<div class="text-wrap">	
												<p><b class="donate-success">$210,851</b> raised by 25 donors</p>
										</div>
									</div>
								</div>
							</div>
							<div class="init-hidden">
								<div class="group-details">
									<div class="top-strip">
										<span>From 04/07/2020 Until 05/07/2020</span>
									</div>
									<div class="group-details-inner">
										<div class="detail-column progress-block donate-success-detail">
											<div class="text-wrap">
												<p><b>$210,851</b> raised by <b>25 Donors</b></p>
											</div>
										</div>
									</div>
								</div>
								<div class="card-action-block">
									<div class="left-btn">
										<b-link class="btn-bordersecondary icon-btn">
											<span>View details</span>
											<i class="icon-wrap">
												<svg width="18" height="9" viewBox="0 0 18 9" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M17.3571 4.54129C17.3571 4.63504 17.3237 4.7154 17.2567 4.78237L13.3996 8.33817C13.2924 8.43192 13.1752 8.45201 13.048 8.39844C12.9208 8.33817 12.8571 8.24107 12.8571 8.10714V5.85714H0.321429C0.227679 5.85714 0.15067 5.82701 0.0904018 5.76674C0.0301339 5.70647 0 5.62946 0 5.53571V3.60714C0 3.51339 0.0301339 3.43638 0.0904018 3.37612C0.15067 3.31585 0.227679 3.28571 0.321429 3.28571H12.8571V1.03571C12.8571 0.895089 12.9208 0.797991 13.048 0.744419C13.1752 0.690848 13.2924 0.707589 13.3996 0.794642L17.2567 4.31027C17.3237 4.37723 17.3571 4.45424 17.3571 4.54129Z" fill="#ffffff"/>
												</svg>
											</i>
										</b-link>
									</div>
									<div class="social-btn">
										<b-button class="icon-btn"><img src="../assets/images/multi-user-icon.svg" alt="multi user icon"></b-button>
										<b-button class="icon-btn"><img src="../assets/images/heart-icon.svg" alt="heart icon"></b-button>
										<!-- <b-button class="icon-btn fill-heart-btn"><img src="../assets/images/heart-fill-icon.svg" alt="heart icon"></b-button> -->
									</div>
								</div>
							</div>
            			</b-card-body>
            		 </b-card>
            		</div>
				</b-col>
				<b-col lg="4" sm="6" class="card-outer" id="grid-card09">
					<div class="card-inner">
					 <b-card no-body class="donate-card">
					 	<div class="location">
							<i>
								<img :src="$store.state.imagePath+'/assets/images/location.svg'" :alt="languageData.label.location">
							</i>
							Paris
						</div>
            			<b-card-header>
            				 <div class="header-img-block">
            				 	<div class="group-img" :style="{backgroundImage: 'url('+grpImages[4]+')'}">
								</div>
            				 </div>
            				 <div class="group-category"><span class="category-text">Culture</span></div>
            			</b-card-header>
            			<b-card-body>
            				<div class="content-block">
								<div class="mission-label-wrap">
									<div class="mission-label donation-label">
										<span><i class="icon-wrap"><img src="../assets/images/donation-icon.svg" alt=""></i>Donation</span>
									</div>
								</div>
								<div class="content-inner-block">
									<b-link target="_blank" class="card-title mb-2">
										Donate $50 for art school
									</b-link>
									<div class="group-ratings">
										<star-rating  v-bind:increment="0.5" v-bind:max-rating="5"
													 inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18"
													  :read-only="true" rating="4.5">
										</star-rating>
									</div>
									<b-card-text>
										Take this chance to take care of abused and abandoned pets at a center located in the Greater Vancouver area of Canada.
									</b-card-text>
								</div>
								<div class="event-block has-progress">
									<p class="event-name">For <span>Artists Club SRL</span></p>
									<div class="progress-block detail-column success-donate" >
										<div class="text-wrap">	
												<p><b class="donate-success">$210,851</b> raised by 25 donors</p>
										</div>
									</div>
									<b-button class="like-btn">
										<img src="../assets/images/heart-fill-icon.svg" alt="Heart Icon"/>
									</b-button>
								</div>
							</div>
							<div class="init-hidden">
								<div class="group-details">
									<div class="top-strip">
										<span>From 04/07/2020 Until 05/07/2020</span>
									</div>
									<div class="group-details-inner">
										<div class="detail-column donate-info">
											<div class="text-wrap">
												<p><span>70%</span> <span>Achieved</span></p>
											</div>
											<div class="text-wrap donate-price">
												<p><span><b>$210,851</b></span> <span>raised by 25 Donors</span></p>
											</div>
											<div class="text-wrap">
												<p><span>$250,000</span> <span>Goal</span></p>
											</div>
										</div>
									</div>
								</div>
								<div class="card-action-block">
									<div class="left-btn">
										<b-link class="btn-bordersecondary icon-btn">
											<span>View details</span>
											<i class="icon-wrap">
												<svg width="18" height="9" viewBox="0 0 18 9" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M17.3571 4.54129C17.3571 4.63504 17.3237 4.7154 17.2567 4.78237L13.3996 8.33817C13.2924 8.43192 13.1752 8.45201 13.048 8.39844C12.9208 8.33817 12.8571 8.24107 12.8571 8.10714V5.85714H0.321429C0.227679 5.85714 0.15067 5.82701 0.0904018 5.76674C0.0301339 5.70647 0 5.62946 0 5.53571V3.60714C0 3.51339 0.0301339 3.43638 0.0904018 3.37612C0.15067 3.31585 0.227679 3.28571 0.321429 3.28571H12.8571V1.03571C12.8571 0.895089 12.9208 0.797991 13.048 0.744419C13.1752 0.690848 13.2924 0.707589 13.3996 0.794642L17.2567 4.31027C17.3237 4.37723 17.3571 4.45424 17.3571 4.54129Z" fill="#ffffff"/>
												</svg>
											</i>
										</b-link>
									</div>
									<div class="social-btn">
										<b-button class="icon-btn"><img src="../assets/images/multi-user-icon.svg" alt="multi user icon"></b-button>
										<!-- <b-button class="icon-btn"><img src="../assets/images/heart-icon.svg" alt="heart icon"></b-button> -->
										<b-button class="icon-btn fill-heart-btn"><img src="../assets/images/heart-fill-icon.svg" alt="heart icon"></b-button>
									</div>
								</div>
							</div>
            			</b-card-body>
            		 </b-card>
            		</div>
				</b-col>
				<b-col lg="4" sm="6" class="card-outer" id="grid-card10">
					<div class="card-inner">
					 <b-card no-body class="donate-card">
					 	<div class="location">
							<i>
								<img :src="$store.state.imagePath+'/assets/images/location.svg'" :alt="languageData.label.location">
							</i>
							Paris
						</div>
            			<b-card-header>
            				 <div class="header-img-block">
            				 	<div class="group-img" :style="{backgroundImage: 'url('+grpImages[4]+')'}">
								</div>
            				 </div>
            				 <div class="group-category"><span class="category-text">Culture</span></div>
            			</b-card-header>
            			<b-card-body>
            				<div class="content-block">
								<div class="mission-label-wrap">
									<div class="mission-label donation-label">
										<span><i class="icon-wrap"><img src="../assets/images/donation-icon.svg" alt=""></i>Donation</span>
									</div>
								</div>
								<div class="content-inner-block">
									<b-link target="_blank" class="card-title mb-2">
										Donate $50 for art school
									</b-link>
									<div class="group-ratings">
										<star-rating  v-bind:increment="0.5" v-bind:max-rating="5"
													 inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18"
													  :read-only="true" rating="4.5">
										</star-rating>
									</div>
									<b-card-text>
										Take this chance to take care of abused and abandoned pets at a center located in the Greater Vancouver area of Canada.
									</b-card-text>
								</div>
								<div class="event-block has-progress">
									<p class="event-name">For <span>Artists Club SRL</span></p>
									<div class="progress-block detail-column success-donate" >
										<div class="text-wrap">	
												<p><b class="donate-success">$210,851</b> raised by 25 donors</p>
										</div>
									</div>
									<b-button class="like-btn">
										<img src="../assets/images/heart-fill-icon.svg" alt="Heart Icon"/>
									</b-button>
								</div>
							</div>
							<div class="init-hidden">
								<div class="group-details">
									<div class="top-strip">
										<span>From 04/07/2020 Until 05/07/2020</span>
									</div>
									<div class="group-details-inner">
										<div class="detail-column donate-info">
											<div class="text-wrap">
												<p><span>70%</span> <span>Achieved</span></p>
											</div>
											<div class="text-wrap donate-price">
												<p><span><b>$210,851</b></span> <span>raised by 25 Donors</span></p>
											</div>
											<div class="text-wrap">
												<p><span>$250,000</span> <span>Goal</span></p>
											</div>
										</div>
									</div>
								</div>
								<div class="card-action-block">
									<div class="left-btn">
										<b-link class="btn-bordersecondary icon-btn">
											<span>View details</span>
											<i class="icon-wrap">
												<svg width="18" height="9" viewBox="0 0 18 9" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M17.3571 4.54129C17.3571 4.63504 17.3237 4.7154 17.2567 4.78237L13.3996 8.33817C13.2924 8.43192 13.1752 8.45201 13.048 8.39844C12.9208 8.33817 12.8571 8.24107 12.8571 8.10714V5.85714H0.321429C0.227679 5.85714 0.15067 5.82701 0.0904018 5.76674C0.0301339 5.70647 0 5.62946 0 5.53571V3.60714C0 3.51339 0.0301339 3.43638 0.0904018 3.37612C0.15067 3.31585 0.227679 3.28571 0.321429 3.28571H12.8571V1.03571C12.8571 0.895089 12.9208 0.797991 13.048 0.744419C13.1752 0.690848 13.2924 0.707589 13.3996 0.794642L17.2567 4.31027C17.3237 4.37723 17.3571 4.45424 17.3571 4.54129Z" fill="#ffffff"/>
												</svg>
											</i>
										</b-link>
									</div>
									<div class="social-btn">
										<b-button class="icon-btn"><img src="../assets/images/multi-user-icon.svg" alt="multi user icon"></b-button>
										<!-- <b-button class="icon-btn"><img src="../assets/images/heart-icon.svg" alt="heart icon"></b-button> -->
										<b-button class="icon-btn fill-heart-btn"><img src="../assets/images/heart-fill-icon.svg" alt="heart icon"></b-button>
									</div>
								</div>
							</div>
            			</b-card-body>
            		 </b-card>
            		</div>
				</b-col>
			</b-row>
		</div>
	</div>
</template>

<script>
	import store from "../store";
	import constants from "../constant";
	import StarRating from "vue-star-rating";
	import {
		favoriteMission,
		inviteColleague,
		applyMission
	} from "../services/service";
	import {
		VueAutosuggest
	} from "vue-autosuggest";
	import moment from "moment";
	export default {
		name: "MissionGridView",
		components: {
			StarRating,
			VueAutosuggest
		},
		props: {
			items: Array,
			userList: Array,
			relatedMission: Boolean
		},
		data() {
			return {
				query: "",
				selected: "",
				myclass: ["userdetail-modal"],
				currentMissionId: 0,
				invitedUserId: 0,
				showErrorDiv: false,
				message: null,
				classVariant: "success",
				autoSuggestPlaceholder: "",
				submitDisable: true,
				languageData: [],
				isInviteCollegueDisplay: true,
				isStarRatingDisplay: true,
				isSubmitNewMissionSet: true,
				isThemeSet: true,
				submitNewMissionUrl : '',
				max: 100,
      			value: 80,
				 grpImages: [
			        // require("@/assets/images/card-img01.png"),
			        // require("@/assets/images/card-img02.png"),
			        // require("@/assets/images/card-img03.png"),
			        // require("@/assets/images/card-img04.png"),
			        // require("@/assets/images/card-img05.png"),
			        // require("@/assets/images/card-img06.png"),
			        // require("@/assets/images/card-img07.png"),
			        // require("@/assets/images/card-img08.png")
			    ],
			};
		},
		computed: {
			filteredOptions() {
				if (this.userList) {
					return [{
						data: this.userList.filter(option => {
							let firstName = option.first_name.toLowerCase();
							let lastName = option.last_name.toLowerCase();
							let email = option.email.toLowerCase();
							let searchString = firstName + "" + lastName + "" + email;
							return searchString.indexOf(this.query.toLowerCase()) > -1;
						})
					}];
				}
			}
		},
		methods: {

			hideModal() {
				this.autoSuggestPlaceholder = ""
				this.submitDisable  = true
				this.invitedUserId  = ""
				this.query = ""
				this.selected = ""
			},
			getAppliedStatus(missionDetail) {
				let currentDate = moment().format("YYYY-MM-DD");
				let missionEndDate = moment(missionDetail.end_date).format("YYYY-MM-DD");
				let checkEndDateExist = true;
				if (missionDetail.end_date != "" && missionDetail.end_date != null) {
					if (currentDate > missionEndDate) {
						checkEndDateExist = false;
					}
				}
				if (missionDetail.user_application_count == 1 && checkEndDateExist) {
					return true;
				}
			},
			getClosedStatus(missionDetail) {
				let currentDate = moment().format("YYYY-MM-DD");
				let missionEndDate = moment(missionDetail.end_date).format("YYYY-MM-DD");
				if (missionDetail.end_date != "" && missionDetail.end_date != null) {
					if (currentDate > missionEndDate) {
						return true;
					}
				}
			},
			//No record found
			noRecordFound() {
				let defaultLang = store.state.defaultLanguage.toLowerCase();
				if (JSON.parse(store.state.missionNotFoundText) != "") {
					let missionNotFoundArray = JSON.parse(store.state.missionNotFoundText);
					let data = missionNotFoundArray.filter((item) => {
						if (item.lang == defaultLang) {
							return item;
						}
					});

					if (data[0] && data[0].message) {
						return data[0].message;
					} else {
						return this.languageData.label.no_record_found;
					}
				} else {
					return this.languageData.label.no_record_found;
				}
			},

			handleFav() {
				let btn_active = document.querySelector(".favourite-icon");
				btn_active.classList.toggle("active");
			},
			//get theme title
			getThemeTitle(translations) {
				if (translations) {
					let filteredObj = translations.filter((item, i) => {
						if (item.lang === store.state.defaultLanguage.toLowerCase()) {
							return translations[i].title;
						}
					});

					if (filteredObj[0]) {
						return filteredObj[0].title;
					} else {

						let filtereObj = translations.filter((item, i) => {
							if (item.lang === store.state.defaultTenantLanguage.toLowerCase()) {
								return translations[i].title;
							}
						});

						if (filtereObj[0]) {
							return filtereObj[0].title;
						}
					}
				}
			},
			// Is default media is video or not
			checkDefaultMediaFormat(mediaType) {
				return mediaType != constants.YOUTUBE_VIDEO_FORMAT;
			},
			// Check mission type
			checkMissionTypeTime(missionType) {
				return missionType == constants.MISSION_TYPE_TIME;
			},
			// Get Youtube Thumb images
			youtubeThumbImage(videoPath) {
				let data = videoPath.split("=");
				return (
						"https://img.youtube.com/vi/" + data.slice(-1)[0] + "/mqdefault.jpg"
				);
			},
			// Add mission to favorite
			favoriteMission(missionId) {
				let missionData = {
					mission_id: ""
				};
				missionData.mission_id = missionId;
				favoriteMission(missionData).then(response => {
					if (response.error == true) {
						this.makeToast("danger", response.message);
					} else {
						this.makeToast("success", response.message);
						this.$emit("getMissions", "removeLoader");
					}
				});
			},
			onInputChange() {
				this.submitDisable = true;
			},
			// For selected user id.
			onSelected(item) {
				if(item) {
					this.selected = item.item;
					this.submitDisable = false;
					this.invitedUserId = item.item.user_id;
				}
			},
			//This is what the <input/> value is set to when you are selecting a suggestion.
			getSuggestionValue(suggestion) {
				let firstName = suggestion.item.first_name;
				let lastName = suggestion.item.last_name;
				return firstName + " " + lastName;
			},
			getMediaPath(mediaPath) {
				if(mediaPath != '') {
					return mediaPath;
				} else {
					return store.state.imagePath+'/assets/images/'+constants.MISSION_DEFAULT_PLACEHOLDER;
				}
			},
			// Open auto suggest modal
			handleModal(missionId) {
				this.autoSuggestPlaceholder = this.languageData.placeholder.search_user;
				this.showErrorDiv = false;
				this.message = null;
				this.$refs.userDetailModal.show();
				this.currentMission = missionId;
				setTimeout(() => {
					this.$refs.autosuggest.$refs.inputAutoSuggest.focus();
					var input = document.getElementById("autosuggest__input");
					input.addEventListener("keyup", (event) => {
						if (event.keyCode === 13 && !this.submitDisable) {
							event.preventDefault();
							this.inviteColleagues()
						}
					});
				}, 100);
			},
			// invite collegues api call
			inviteColleagues() {
				let inviteData = {};
				inviteData.mission_id = this.currentMission;
				inviteData.to_user_id = this.invitedUserId;
				inviteColleague(inviteData).then(response => {
					this.submitDisable = true;
					if (response.error == true) {
						this.classVariant = "danger";
						this.message = response.message;
						this.$refs.autosuggest.$data.currentIndex = null;
						this.$refs.autosuggest.$data.internalValue = "";
						this.showErrorDiv = true;
					} else {
						this.query = "";
						this.selected = "";
						this.currentMissionId = 0;
						this.invitedUserId = 0;
						this.$refs.autosuggest.$data.currentIndex = null;
						this.$refs.autosuggest.$data.internalValue = "";
						this.classVariant = "success";
						this.message = response.message;
						this.showErrorDiv = true;
					}
				});
			},
			// Apply for mission
			applyForMission(mission) {
				let missionData = {};
				missionData.mission_id = mission.mission_id;
				missionData.availability_id = mission.availability_id;
				applyMission(missionData).then(response => {
					if (response.error == true) {
						this.makeToast("danger", response.message);
					} else {
						this.makeToast("success", response.message);
						this.$emit("getMissions");
					}
				});
			},
			makeToast(variant = null, message) {
				this.$bvToast.toast(message, {
					variant: variant,
					solid: true,
					autoHideDelay: 1000
				});
			},
			submitNewMission() {
				if(this.submitNewMissionUrl != '') {
					window.open(this.submitNewMissionUrl, '_self');
				}
			},
			cardHeightAdj() {
				setTimeout(function () {
				var cardBody = document.querySelectorAll(".card-grid .card-body");
				var cardText = document.querySelectorAll(".card-grid .card-body .card-text");

					// cardText.forEach(function (textEvent) {
					// 	var cardTextH = textEvent.offsetHeight;
					// 	console.log("max height",Math.max(...cardTextH));
					// 	// textEvent.style.height =  Math.max.apply( Math, cardTextH) + 'px';
					// });

					cardBody.forEach(function (event) {
						var getCard = event.parentNode;
						var cardHeight = event.children[0].offsetHeight + getCard.children[1].offsetHeight;
						var cardOuterHeight = event.parentNode.parentNode.offsetHeight - getCard.children[0].offsetHeight + event.children[0].children[1].offsetHeight + event.children[0].children[0].offsetHeight;
						// console.log('card body', event.children[0].children[1].offsetHeight);
						// console.log("card inner height", event.parentNode.parentNode.offsetHeight - getCard.children[0].offsetHeight);
							getCard.style.height = cardHeight +"px";	
						// if(cardHeight == cardOuterHeight){
						// }
						// else{
						// 	getCard.style.height = cardOuterHeight +"px";	
						// }
						event.parentNode.addEventListener("mouseover", function (mouseEvent) {
						var cardBodyH = this.children[2].children[1].offsetHeight + this.children[2].children[0].offsetHeight + this.children[1].offsetHeight;
						var cardTotalHeight = cardBodyH - this.offsetHeight;

							this.children[1].style.transform = "translateY(-" + cardTotalHeight + "px)";
							this.children[2].style.transform = "translateY(-" + cardTotalHeight + "px)";
							this.parentNode.classList.add("active");
		                });
						event.parentNode.addEventListener("mouseleave", function () {
							this.children[1].style.transform = "translateY(0)";
							this.children[2].style.transform = "translateY(0)";
							this.parentNode.classList.remove("active");
		                });
					});
				},500);
			}
		},
		created() {
			this.languageData = JSON.parse(store.state.languageLabel);
			this.isInviteCollegueDisplay = this.settingEnabled(
					constants.INVITE_COLLEAGUE
			);
			this.isStarRatingDisplay = this.settingEnabled(constants.MISSION_RATINGS);
			this.isSubmitNewMissionSet = this.settingEnabled(
					constants.USER_CAN_SUBMIT_MISSION
			);
			this.isThemeSet = this.settingEnabled(constants.THEMES_ENABLED);
			this.submitNewMissionUrl = store.state.submitNewMissionUrl

			this.cardHeightAdj();
			var _this = this;
			window.addEventListener("resize", this.cardHeightAdj());
			var tabButton = document.querySelectorAll(".view-tab .nav-link");
			tabButton.forEach(function(btnEvent){
				btnEvent.addEventListener("click", _this.cardHeightAdj());
			});
		},
		// updated() {
  //     		this.cardHeightAdj();
  //     		console.log("updated")
  //   	}
	};
</script>
