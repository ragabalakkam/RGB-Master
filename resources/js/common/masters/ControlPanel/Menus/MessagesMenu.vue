<template>
  <b-menu class="messages-menu ht-390 wd-280">
    <template v-slot:header>
      <div class="p-3">
        <div class="d-flex align-items-center mb-2">
          <p
            class="h5 flex-1 mb-0 text-capitalize"
            v-text="$t('ال') + $t('messages')"
          ></p>
          <span
            class="btn btn-outline-info btn-sm text-white border-white py-0 px-1 bg-hover-white text-hover-info"
            v-t="'markAllAsRead'"
          />
        </div>
        <div
          class="fs-3"
          v-text="`4 ${$t('unreadX', { attr: $t('messages') })}`"
        />
      </div>
    </template>
    <template v-slot:body>
      <div
        class="p-3 border-bottom border-light d-flex align-items-center bg-hover-light c-ptr"
      >
        <div class="sz-45 bg-secondary rounded-circle mr-3"></div>
        <div class="flex-1">
          <div>menu1</div>
          <div class="fs-3 text-secondary">
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
          </div>
        </div>
      </div>
      <div
        class="p-3 border-bottom border-light d-flex align-items-center bg-hover-light c-ptr"
      >
        <div class="sz-45 bg-secondary rounded-circle mr-3"></div>
        <div class="flex-1">
          <div>menu1</div>
          <div class="fs-3 text-secondary">
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
          </div>
        </div>
      </div>
      <div
        class="p-3 border-bottom border-light d-flex align-items-center bg-hover-light c-ptr"
      >
        <div class="sz-45 bg-secondary rounded-circle mr-3"></div>
        <div class="flex-1">
          <div>menu1</div>
          <div class="fs-3 text-secondary">
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
          </div>
        </div>
      </div>
      <div
        class="p-3 d-flex align-items-center overflow-hidden bg-hover-light c-ptr"
      >
        <div class="sz-45 bg-secondary rounded-circle mr-3"></div>
        <div class="flex-1">
          <div>menu1</div>
          <div class="fs-3 text-secondary">
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
          </div>
        </div>
      </div>
    </template>
    <template v-slot:footer>
      <b-button
        variant="light"
        block
        class="border-0 px-3 py-1 text-center fs-3"
        v-t="'viewAll'"
      />
    </template>
  </b-menu>
</template>

<script>
export default {
  name: "messages-menu",
};
</script>