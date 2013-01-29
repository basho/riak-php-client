<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0">
	<xsl:output method="html" />

	<xsl:key name="sources" match="/checkstyle/file/error" use="@source" />

	<xsl:template match="checkstyle" mode="phpcs-summary">
		<p />
		<table class="result" align="center">
			<colgroup>
				<col width="1%"></col>
				<col width="85%"></col>
				<col width="5%"></col>
				<col width="9%"></col>
			</colgroup>
			<thead>
				<tr>
					<th colspan="2">PHP CodeSniffer violation</th>
					<th>Files</th>
					<th>Errors</th>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each
					select="file/error[generate-id() = generate-id(key('sources', @source)[1])]">
					<xsl:sort data-type="number" order="descending"
						select="count(key('sources', @source))" />
					<xsl:variable name="errorCount" select="count(key('sources', @source))" />
					<xsl:variable name="fileCount"
						select="count(../../file[error/@source=current()/@source])" />
					<tr>
						<xsl:if test="position() mod 2 = 1">
							<xsl:attribute name="class">oddrow</xsl:attribute>
						</xsl:if>
						<td></td>
						<td>
							<xsl:value-of select="@source" />
						</td>
						<td align="right">
							<xsl:value-of select="$fileCount" />
						</td>
						<td align="right">
							<xsl:value-of select="$errorCount" />
						</td>
					</tr>
				</xsl:for-each>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2"></td>
					<td align="right">
						<xsl:value-of select="count(file[error])" />
					</td>
					<td align="right">
						<xsl:value-of select="count(file/error)" />
					</td>
				</tr>
			</tfoot>
		</table>
	</xsl:template>

</xsl:stylesheet>