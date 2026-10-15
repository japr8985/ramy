import settingsService from "@/api/settings.api";
import { SettingsMap } from "@/types/settings";
import { defineStore } from "pinia";
import { Ref, ref } from "vue";

export const settingStore = defineStore('setting', () => {
    const settings: Ref<SettingsMap> = ref({} as SettingsMap);
    
    const get = async () => {
        const set = await settingsService.getSettings()
        settings.value = set;
    }

    return {
        settings,
        get
    }
})