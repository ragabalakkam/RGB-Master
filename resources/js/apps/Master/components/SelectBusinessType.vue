<template>
  <b-form-group v-bind="$attrs">
		<div class="bg-light border rounded p-3 d-flex flex-gap-3 flex-column-xs" :class="`min-ht-${wXS ? business_type ? 320 : 200 : 265}`">
			
			<div class="flex-1 d-flex flex-column">
				<div class="d-flex align-items-end flex-gap-2">
					<b-select
						class="flex-1"
						:data="businessTypes"
						:label="$t('theBusinessType')"
						:errors="errors"
						name="business_type_id"
						attr="theBusinessType"
						:cast-text="(x) => parseName(x)"
						:null-option-attr="$t('businessType')"
						v-model="business_type_id"
					/>
					<a
						v-if="business_type"
						class="ht-form-control btn bg-white no-shadow border"
						:href="`/master/business-types/${business_type.id}`"
						target="_blank"
					>
						<b-i icon="external-link" />
					</a>
				</div>
				<p
					class="p-3 bg-white rounded border flex-1 text-secondary-8 mt-2"
					v-text="business_type && business_type.description ? business_type.description : `${$t('thereIsNoX', { x: $t('description') })} ..`"
				/>
			</div>
			
			<div v-if="business_type" class="d-flex flex-gap-2" :style="`justify-content: ${wXS ? 'space-between' : 'end'}`">
				<div v-for="device in ['desktop', 'tablet', 'mobile']" :key="device" class="bg-white rounded border d-flex flex-column">
					<p class="border-bottom px-2 py-1 font-md">
						<b-i :icon="device" class="mr-1" />
						<span v-t="device" />
					</p>
					<div class="p-1" :class="`ht-${wXS ? 90 : 200} min-wd-${wXS ? 100 : 170}`">
						<img
							v-if="business_type"
							v-open-in-viewer
							class="h-100"
							:src="`/storage/${business_type.cashier_settings[`${device}_screenshot`]}`"
						/>
					</div>
				</div>
			</div>
			
		</div>
  </b-form-group>
</template>

<script>
import BSelect from "../../../common/components/Inputs/Select/BSelect.vue";
export default {
  name: "select-business-type",
  props: {
    value					: { required: true },
    errors				: { default: () => {} },
    businessTypes	: { default: {} },
  },
  computed: {
    business_type_id: {
      set(val) {
        this.$emit("input", val);
		this.$emit("change", val);
      },
      get() {
        return this.value;
      },
    },
	business_type() {
		return this.business_type_id && this.businessTypes[this.business_type_id];
	},
  },
  components: { BSelect },
};
</script>