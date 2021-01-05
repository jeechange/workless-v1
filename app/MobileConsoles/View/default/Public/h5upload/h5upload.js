(function ($, win) {
    var H5Upload = function (element, options) {
        this.element = element;
        this.elementId = $(element).attr("id");
        this.elementName = $(element).attr("name");
        this.options = options;

    };

    win.removePicItem = function () {
        var picItem = $(this).parents(".file-queue");
        picItem.remove();
    };
    win.uploadPhotoBrowser = function (val) {
        if (!this.PhotoBrowser) {
            this.PhotoBrowser = $.photoBrowser({
                photos: [val]
            });
        }
        this.PhotoBrowser.open();
    };

    H5Upload.prototype = {
        chunkSize: 128 * 1024,
        fileIndex: 0,
        handleInputChange: function () {
            for (var i = 0; i < this.element.files.length; i++) {
                var FileChunk = [];
                var file = this.element.files[i];
                var BufferChunkSize = this.chunkSize;
                var FileStreamPos = 0;
                var EndPos = BufferChunkSize;
                var Size = file.size;
                while (FileStreamPos < Size) {
                    FileChunk.push(file.slice(FileStreamPos, EndPos));
                    FileStreamPos = EndPos; // jump by the amount read
                    EndPos = FileStreamPos + BufferChunkSize; // set next chunk length
                }
                file.id = this.fileIndex;
                this.fileQueued(file);
                this.upload(file, this.fileIndex, FileChunk, 0, FileChunk.length);
                this.fileIndex++;
            }
        },
        fileQueued: function (file) {
            var queueId = this.elementId + "_" + file.id;
            var $queue = $("#" + queueId);
            if ($queue.length === 0) {
                $queue = $("<div id='" + queueId + "' class='file-queue'>0%</div>");
                $(this.element).parent().before($queue);
            } else {
                $queue.html("");
            }
        },
        uploadProgress: function (file, percentage) {
            var queueId = this.elementId + "_" + file.id;
            var $queue = $("#" + queueId);
            $queue.text(parseFloat(percentage * 100).toFixed(2) + "%");
        },
        uploadDone: function (file, result) {

            var queueId = this.elementId + "_" + file.id;

            var $queue = $("#" + queueId);

            var html = [
                '<div class="pics-added-item">',
                '<span class="pics-added-item-result" onclick="uploadPhotoBrowser.call(this,\'' +
                this.options.cdnBase + result.saveName + '\')"><img src="',
                this.options.cdnThumbBase + result.saveName,
                '"></span>',
                '<span class="pics-added-item-remove" onclick="removePicItem.call(this,\'',
                result.saveName,
                '\');">×</span>',
                "<input class='img-uploader-input' type='hidden' name='" + this.elementName + "[" + file.id + "]' value='" + result.saveName + "' />",
                "<input class='img-uploader-show' type='hidden' name='" + this.elementName + "_show[" + file.id + "]' value='" + result.sourceName + "' />",
                '</div>'
            ].join("");
            $queue.html(html);
        },
        uploadError: function (file, xhr) {

        },
        upload: function (file, fileIndex, FileChunk, PartCount, TotalParts) {
            var formData = new FormData();

            var Chunk = FileChunk.shift();
            // size
            formData.append('fromDomain', location.hostname);
            formData.append('chunk', PartCount);
            formData.append('chunks', TotalParts);
            formData.append('chunkName', file.name);
            formData.append('file', Chunk, file.name);
            formData.append('apply', this.options.apply);
            formData.append('sid', this.options.sid);
            formData.append('options', this.options.options);
            formData.append('uid', this.options.uid);
            var _this = this;
            $.ajax({
                type: "POST",
                url: this.options.url,
                contentType: false,
                processData: false,
                data: formData,
                dataType: "json",
                success: function (data) {
                    PartCount++;
                    if (PartCount < TotalParts) {
                        _this.upload(file, fileIndex, FileChunk, PartCount, TotalParts);
                        _this.uploadProgress(file, PartCount / TotalParts);
                    } else {
                        _this.uploadDone(file, data);
                    }
                },
                error: function (data) {
                    _this.uploadError(data);
                }
            });
        }
    };


    $.fn.h5upload = function (option) {
        return this.each(function () {
            var $this = $(this),
                data = $this.data('h5upload'),
                options = typeof option === 'object' && option;
            if (!data) {
                $this.data('h5upload', (data = new H5Upload(this, $.extend({}, $.fn.h5upload.defaults, options))));
            }
            if (typeof option === 'string') {
                data[option]();
            }
        });
    };
    $.fn.h5upload.defaults = {
        apply: "mobileConsoles",
        sid: 0,
        uid: 0,
        options: 0,
        url: 'https://cdn.itmakes.com/uploader.php',
        cdnThumbBase: 'https://cdn.itmakes.com/thumbs',
        cdnBase: 'https://cdn.itmakes.com/uploads',
    };

    $(document).on('change', '[data-toggle^=h5upload]', function (e) {
        $(this).h5upload('handleInputChange');
    });
})(Zepto, window);

