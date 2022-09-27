<template>
  <div>
    <v-app-bar
      app
      color="primary"
      dark
      class="justify-end"
    >
        <div class="flex-grow-1"></div>

        <v-row class="justify-end" v-if="!authStatus">
            <v-btn
                :href="this.host + 'login'"
                color="success"
            >ログイン</v-btn>
            <v-btn
                :href="this.host + 'register'"
                class="ml-2"
                color="warning"
            >新規登録</v-btn>
        </v-row>
        <v-row class="justify-end" v-else>
            <v-btn
                @click="logoutBtn"
                color="error"
            >ログアウト</v-btn>
        </v-row>
        <v-spacer></v-spacer>
    </v-app-bar>
  </div>
</template>

<script>
export default {
  name: 'HeaderGlobal',
  data: ()=> ({
  }),
  methods: {
      async logoutBtn(){
          const respons = await fetch(this.host + 'logout', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': this.csrfToken(),
              },
          })
          .then(response => {
              console.log(response)
              window.location.href = this.host;
          })
          .catch(error => {
              console.log(error);
          });
      },
      csrfToken(){
          const token = document.head.querySelector('meta[name="csrf-token"]');
          return token instanceof HTMLMetaElement ? token.content : '';
      }
  },
  mounted() {
  },
  props: {
      host: String,
      authStatus: Boolean
  },
}
</script>

<style scoped>
.menu_bar{
  list-style: none;
  padding: 0;
  background-color: red;
  position: absolute
}
.menu_bar li{
    display: inline-block;
}

.menu_bar a{
  color: #FFF;
}

.menu_bar li:nth-of-type(1){
    margin-right: 10px;
}
</style>