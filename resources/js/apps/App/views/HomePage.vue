<template>
  <div class="home-page h-100 overflow-scroll" style="color: var(--home-color)">
    <!-- Page 1 | Main -->
    <section class="position-relative h-100">
      <b-gradient
        from="var(--home-from)"
        to="var(--home-to)"
        dir="var(--home-dir)"
        class="position-relative overflow-hidden"
      >
        <!-- background logo -->
        <b-img
          class="position-absolute position-center mb-4 mx-auto"
          style="transform: translate(-50%, -50%) rotate(-30deg); opacity: 0.05;"
          size="2300"
          :src="`/storage/${app.logo}`"
        />

        <!-- bright circles -->
        <div class="shape shape-style-1 shape-primary">
          <span class="span-150"></span>
          <span class="span-50"></span>
          <span class="span-50"></span>
          <span class="span-75"></span>
          <span class="span-100"></span>
          <span class="span-75"></span>
          <span class="span-50"></span>
          <span class="span-100"></span>
          <span class="span-50"></span>
          <span class="span-100"></span>
        </div>

        <!-- main content -->
        <div class="position-absolute position-top-left h-100 w-100 d-flex-center">
          <div class="container" style="color:#22427B">
            <div class="row">
              <div class="col-md-6" style="z-index:1">
                <div :class="`bg-light-${wXS ? 7 : 3} bg-hover-light c-ptr rounded-lg p-5`">
                  <div class="mb-4" style="color:#243863">
                    <p class="h1 mb-4" v-html="$t('homepage.org.name')" />
                    <p class="fs-5" :class="wXS ? 'min-wd-350' : 'max-wd-400'" v-html="$t('homepage.org.slogan')" />
                  </div>
                  <div class="fs-4 mb-4" style="color:#2B468A" v-html="$t('homepage.org.description')" />
                  <div v-if="!user" class="d-flex flex-gap-3" :class="{ 'justify-content-center' : wXS }">
                    <b-router class="btn btn-success px-3 py-2 fs-4 text-uppercase" :to="'/client/register'" v-text="$t('xForFree', { x: `${$t('register')} ${$t('now')}` })" />
                    <b-router class="btn btn-primary px-3 py-2 fs-4 text-uppercase" :to="'/client/login'" v-text="$t('login')" />
                  </div>  
                  <b-router to="/redirect" v-else class="btn btn-light px-3 py-2 fs-4 text-uppercase" style="color:#2B468A">
                    {{ $t('getStarted') }} {{ $t('now') }}
                    <b-i icon="arrow-right" class="ml-2" />
                  </b-router>
                </div>
              </div>
              <div class="position-absolute position-right" :style="`height: 100%; width: 55%; top: 45vh; ${getLocale() == 'ar' ? 'left' : 'right'}: -2%;`">
                <img style="width:65vw" src="/imgs/homepage.png" />
              </div>
            </div>
          </div>
        </div>

        <!-- social media links -->
        <div
          v-if="false"
          class="position-absolute mb-5 text-inherit fs-5"
          :class="`position-bottom${wXS ? '' : '-left ml-5'}`"
        >
          <div class="d-flex flex-gap-2 flex-gap-md-3" :class="wXS ? 'w-max-content mx-auto' : 'flex-column ml-auto'">
            <a
              v-for="(link, index) in social_media_links"
              :key="index"
              :href="link.url"
              target="_blank"
              class="d-block text-hover-primary px-1"
            >
              <b-i
                :icon="icons[link.name.en] || icons['default']"
                :fas="icons[link.name.en] ? 'fab' : 'fal'"
              />
            </a>
          </div>
          <p class="px-1 pt-1 pt-md-3">Get in touch with us</p>
        </div>
      </b-gradient>
    </section>

    <!-- Page 2 | Apps -->
    <section class="h-100 bg-white d-flex-center">
      <div class="container">
        <p class="h3 mb-5" v-text="$t('XY', { 0: $t('ال') + $t('apps'), 1: $t('ال') + $t('providedByX', { x: $t('XY', { 0: $t('organization'), 1: $t('homepage.org.name') }) }) })" />
        <div class="mb-5" v-for="app in apps" :key="app.id">
          <div class="btn btn-light bg-light-5 btn-block text-left border border-x p-4" :title="parseName(app.name)">
            <div class="d-flex flex-gap-bs align-items-center mb-3">
              <b-img v-if="!wXS" style="flex:none" size="90" :src="parseImg(app)" />
              <div>
                <div class="d-flex align-items-center flex-gap-3 mb-3">
                  <b-img v-if="wXS" style="flex:none" :src="parseImg(app)" />
                  <p class="fs-5 text-primary" v-text="parseName(app.name)" />
                </div>
                <div
                  class="mb-3"
                  style="white-space:pre-wrap"
                  v-text="parseName(app.description)"
                />
                <div class="d-flex flex-gap-2">
                  <b-router
                    class="btn btn-primary py-1 px-2"
                    :to="!user ? '/client/login' : `/client/organizations-apps/create?app_id=${app.id}`"
                    v-text="$t('xForFree', { x: `${$t('subscribe')} ${$t('now')}` })"
                  />
                  <b-router
                    class="btn btn-outline-info text-hover-light py-1 px-2"
                    :to="!user ? `/apps/${app.id}` : `/client/apps/${app.id}`"
                    v-text="$t('readMore')"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Page 3 | Contact us -->
    <section class="h-100 d-flex-center text-dark-8">
      <div class="container">
        <div class="row">
          <div class="col-md-5 d-flex-center">
            <contact-form />
          </div>
          <div v-if="!wXS" class="offset-md-2 col-md-5">
            <b-img size="fill" src="/imgs/contact-us.png" />
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import ContactForm from '../../../common/components/ContactForm.vue';
import AppsShow from "../../Client/views/Apps/Show.vue";
export default {
  name: "home",
  computed: {
    ...mapGetters({
      app: 'configurations/app',
      apps: 'client/apps',
      user: 'auth/user',
      icons: "icons",
      social_media_links: "configurations/social_media_links",
    }),
  },
  created() {
    this.$store.dispatch('client/fetchApps');
    setTimeout(() => {
      let el = document.querySelector('.home-page'),
        head = document.querySelector('.default-master > header'),
        clss = 'scrolling';

      el.addEventListener('scroll', e => {
        if (el.scrollTop > 150) {
          head.classList.add(clss);
          head.classList.remove('py-4');
        }
        else {
          head.classList.remove(clss);
          head.classList.add('py-4');
        }
      });
      
      head.classList.add('py-4');
    }, 50);
  },
  components: {
    AppsShow,
    ContactForm,
  },
};
</script>

<style lang="scss">
.shape-style-1 {
  span {
    height: 120px;
    width: 120px;
    border-radius: 50%;
    position: absolute;
  }

  .span-200 {
    height: 200px;
    width: 200px;
  }

  .span-150 {
    height: 150px;
    width: 150px;
  }

  .span-100 {
    height: 100px;
    width: 100px;
  }

  .span-75 {
    height: 75px;
    width: 75px;
  }

  .span-50 {
    height: 50px;
    width: 50px;
  }

  :nth-child(1) {
    left: -4%;
    bottom: auto;
    background: rgba(255, 255, 255, 0.1);
  }

  :nth-child(2) {
    right: 4%;
    top: 10%;
    background: rgba(255, 255, 255, 0.1);
  }

  :nth-child(3) {
    top: 280px;
    right: 5.66666%;
    background: rgba(255, 255, 255, 0.3);
  }

  :nth-child(4) {
    top: 320px;
    right: 7%;
    background: rgba(255, 255, 255, 0.15);
  }

  :nth-child(5) {
    top: 38%;
    left: 1%;
    right: auto;
    background: rgba(255, 255, 255, 0.05);
  }

  :nth-child(6) {
    width: 200px;
    height: 200px;
    top: 44%;
    left: 10%;
    right: auto;
    background: rgba(255, 255, 255, 0.15);
  }

  :nth-child(7) {
    bottom: 50%;
    right: 36%;
    background: rgba(255, 255, 255, 0.04);
  }

  :nth-child(8) {
    bottom: 70px;
    right: 2%;
    background: rgba(255, 255, 255, 0.2);
  }

  :nth-child(9) {
    bottom: 1%;
    right: 2%;
    background: rgba(255, 255, 255, 0.1);
  }

  :nth-child(10) {
    bottom: 1%;
    left: 1%;
    right: auto;
    background: rgba(255, 255, 255, 0.05);
  }
}

.scrolling {
  background-color: var(--bs-white);
  color: var(--bs-dark);
}
</style>