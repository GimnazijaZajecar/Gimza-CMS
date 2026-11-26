{{-- Filament нема српски превод за поље за отпремање (FilePond), па га постављамо овде. --}}
<script>
    (() => {
        const labels = {
            labelIdle: 'Превуците фајлове овде или <span class="filepond--label-action">изаберите</span>',
            labelInvalidField: 'Поље садржи неисправне фајлове',
            labelFileWaitingForSize: 'Чека се величина',
            labelFileSizeNotAvailable: 'Величина није позната',
            labelFileLoading: 'Учитавање',
            labelFileLoadError: 'Грешка при учитавању',
            labelFileProcessing: 'Отпремање',
            labelFileProcessingComplete: 'Отпремљено',
            labelFileProcessingAborted: 'Отпремање је прекинуто',
            labelFileProcessingError: 'Грешка при отпремању',
            labelFileProcessingRevertError: 'Грешка при враћању',
            labelFileRemoveError: 'Грешка при уклањању',
            labelTapToCancel: 'кликните за прекид',
            labelTapToRetry: 'кликните за нови покушај',
            labelTapToUndo: 'кликните за поништавање',
            labelButtonRemoveItem: 'Уклони',
            labelButtonAbortItemLoad: 'Прекини',
            labelButtonRetryItemLoad: 'Покушај поново',
            labelButtonAbortItemProcessing: 'Откажи',
            labelButtonUndoItemProcessing: 'Поништи',
            labelButtonRetryItemProcessing: 'Покушај поново',
            labelButtonProcessItem: 'Отпреми',
            labelMaxFileSizeExceeded: 'Фајл је превелик',
            labelMaxFileSize: 'Највећа дозвољена величина је {filesize}',
            labelMaxTotalFileSizeExceeded: 'Прекорачена је укупна величина',
            labelMaxTotalFileSize: 'Највећа укупна величина је {filesize}',
            labelFileTypeNotAllowed: 'Ова врста фајла није дозвољена',
            fileValidateTypeLabelExpectedTypes: 'Дозвољено: {allButLastType} или {lastType}',
            imageValidateSizeLabelFormatError: 'Ова врста слике није подржана',
            imageValidateSizeLabelImageSizeTooSmall: 'Слика је премала',
            imageValidateSizeLabelImageSizeTooBig: 'Слика је превелика',
        };

        document.addEventListener('FilePond:init', (event) => event.detail?.pond?.setOptions(labels));
    })();
</script>
