<template>
 	
  <div class="home">
  	<header>
            <TopHeader></TopHeader>
	</header>
	 <main>
    <b-container>
     </b-container>
	</main>
  </div>
</template>

<script>
import TopHeader from "../components/Header/TopHeader";
import PrimaryFooter from "../components/Footer/PrimaryFooter";

export default {
  name: 'home',
   components: {
    TopHeader,
    PrimaryFooter
},
}
</script>
