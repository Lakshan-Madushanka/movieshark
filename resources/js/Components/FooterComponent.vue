<template>
    <footer class="flex flex-col md:flex-row  gap-2 text-sm justify-between items-center px-8 py-8 bg-gray-800 mt-12">
        <div>© <b>MovieShark</b> - Owned by
            <AnchorLink href="https://lakm.lovestoblog.com" target="_blank" title="Site">LakM</AnchorLink>
        </div>
        <ul class="flex justify-center items-center gap-x-4">
            <li>
                <AnchorLink href="https://github.com/Lakshan-Madushanka/movieshark" target="_blank" title="Github"><i
                    class="pi pi-github"/></AnchorLink>
            </li>
            <li>
                <AnchorLink href="https://x.com/epmadushanka" target="_blank" title="X (formly twitter)"><i
                    class="pi pi-twitter"/></AnchorLink>
            </li>

            <li>
                <AnchorLink href="https://www.linkedin.com/in/lakshan-madushanka" target="_blank" title="Linkedin"><i
                    class="pi pi-linkedin"/></AnchorLink>
            </li>

            <li>
                <AnchorLink href="mailto:epmadushanka@gmail.com" target="_blank" title="X (formly twitter)"><i
                    class="pi pi-at"/></AnchorLink>
            </li>
        </ul>
    </footer>
</template>
<script setup>
import AnchorLink from "@/Components/AnchorLink.vue";
</script>
